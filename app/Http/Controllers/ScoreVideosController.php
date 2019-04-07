<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use View;
use Cookie;
use Carbon\Carbon;
use App\Helpers\Roles;
use Illuminate\Http\Request;

use App\Mail\VideoReport;

use App\Enums\{VideoReviewStatus, VideoType, VideoFlag};

use App\{
	Models\Vid_competition,
	Models\Video_comment,
	Models\Video_scores,
	Models\Video,
	Models\Vid_score_type};

class ScoreVideosController extends Controller {
	public $group_names = [ VideoType::General => "General",
							VideoType::Custom    => "Custom Part",
							VideoType::Compute => "Computational Thinking" ];

	public function __construct()
	{
		parent::__construct();

		//Breadcrumbs::addCrumb('User Videos', 'video/user');
	}

	/**
	 * Display a listing of Video_scores
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$date = Carbon::now()->setTimezone('America/Los_Angeles')->toDateString();
		// Get a list of active Video Competitions
		$competiton = Vid_competition::with('divisions')
								->where('event_start', '<=', $date)
								->where('event_end', '>=', $date)
								->get();

		$comp_list = [];
		$div_list = [];
		foreach($competiton as $comp) {
			foreach($comp->divisions as $div) {
				$comp_list[$comp->name][] = $div->name;
				$div_list[] = $div->id;
			}
		}

		// Get a list of all videos this user has scored
		if(!empty($div_list)) {
			$video_scores = Video_scores::with('division', 'division.competition', 'video', 'video.comments')
								->where('judge_id', Auth::user()->id)
								->whereIn('vid_division_id', $div_list)
								->orderBy('total', 'desc')
								->get();
		} else {
			$video_scores = [];
		}
		$videos = [];
		$types = Vid_score_type::orderBy('id')->pluck('name', 'id')->all();

		// Create blank list of scores
		$blank = array_combine(array_keys($types), array_fill(0, count($types), '-'));
		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->video->name] = $blank;
		}

		// Populate score list with actual scores
		$scored_count = array_combine(array_keys($this->group_names), array_fill(0, count($this->group_names), 0));
		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->video->name][$score->vid_score_type_id] = $score;
			$videos[$score->division->longname()][$score->video->name]['video_id'] = $score->video_id;
			$videos[$score->division->longname()][$score->video->name]['flag'] = $score->video->flag;

			if(count($score->video->comments)) {
			    foreach($score->video->comments as $comment) {
			        $videos[$score->division->longname()][$score->video->name]['comments'] =
			            "<strong>Comment</strong><br>" . $comment->comment;
			        if(!empty($comment->resolution)) {
			            $videos[$score->division->longname()][$score->video->name]['comments'] .=
			            "<br><strong>Resolution</strong><br>" . $comment->resolution . "<br>";
			        }
			    }
			} else {
			    $videos[$score->division->longname()][$score->video->name]['comments'] = '';
			}

			// Only count videos which are not under review/disqualified
			if($score->video->flag == VideoFlag::Normal) {
				$scored_count[$score->score_group]++;
			}
		}

		// Fix category count for VideoType::General
		// After 2017 there is a "theme" entry in general scores
		$general_divisor = 3;
		if($competiton->last() && $competiton->last()->event_start->year > 2017) {
			$general_divisor = 4;
		}
		$scored_count[VideoType::General] /= $general_divisor;

		if(count($div_list) > 0) {
			$total_count[VideoType::General] = Video::whereIn('vid_division_id', $div_list)
				->where('flag', VideoFlag::Normal)
				->where('review_status', VideoReviewStatus::Passed)
				->count();
			$total_count[VideoType::Custom] = Video::whereIn('vid_division_id', $div_list)
				->where('has_custom', true)
				->where('flag', VideoFlag::Normal)
				->where('review_status', VideoReviewStatus::Passed)
				->count();
			$total_count[VideoType::Compute] = Video::whereIn('vid_division_id', $div_list)
				->where('has_code', true)
				->where('flag', VideoFlag::Normal)
				->where('review_status', VideoReviewStatus::Passed)
				->count();
		} else {
			$total_count[VideoType::General] = 0;
			$total_count[VideoType::Custom] = 0;
			$total_count[VideoType::Compute] = 0;
		}

		// Setup toggle boxes based on cookie
		$judge_compute = Cookie::get('judge_compute',0) ? 'checked="checked"' : '';
		$judge_custom = Cookie::get('judge_custom',0) ? 'checked="checked"' : '';

		//ddd($videos);

		View::share('title', 'User Videos');
		return View::make('video_scores.index', compact('videos', 'comp_list', 'types', 'total_count', 'scored_count', 'judge_compute', 'judge_custom'));
	}

	// Choose an appropriate video for judging
	// Display video to be used
	public function do_dispatch(Request $req)
	{
		// Get toggle statuses
		if($req->has('judge_compute')) {
			Cookie::queue('judge_compute', 1, 5 * 365 * 24 * 60 * 60);
			$judge_compute = true;
		} else {
			Cookie::queue('judge_compute', null, -1);
			$judge_compute = false;
		}

		if($req->has('judge_custom')) {
			Cookie::queue('judge_custom', 1, 5 * 365 * 24 * 60 * 60);
			$judge_custom = true;
		} else {
			Cookie::queue('judge_custom', null, -1);
			$judge_custom = false;
		}

		// Get the accurate date
		$date = Carbon::now()->setTimezone('America/Los_Angeles')->toDateString();

		// Get a list of active Video Competitions
		$comps = Vid_competition::with('divisions')
								->where('event_start', '<=', $date)
								->where('event_end', '>=', $date)
								->get();
		$divs = [0];
		foreach($comps as $comp) {
			$divs = array_merge($divs, $comp->divisions->pluck('id')->all());
		}

		// Get all the videos and their scores were the video is not flagged for review or disqualified
		$all_videos = Video::with('scores')
			->where('flag',VideoFlag::Normal)
			->where('review_status', VideoReviewStatus::Passed)
			->whereIn('vid_division_id', $divs)
			->get();

		//dd(DB::getQueryLog());
//		echo "<pre>";
//		foreach($all_videos as $video) {
//			echo $video->id . " - scores: " . count($video->scores) . "<br />";
//		}
//		echo "</pre>";

		// Remove videos which have no scores or which this user has scored before
		$filtered = $all_videos->filter(function($video) {
			if(count($video->scores) == 0) {
				// Videos with no scores stay on the list
				return true;
			} else {
				foreach($video->scores as $score) {
					if($score->judge_id == Auth::user()->id) {
						return false;
					}
				}
				return true;
			}
		});

//		echo "Filtered: <br/><pre>";
//		foreach($filtered as $video) {
//			echo $video->id . " - scores: " . count($video->scores) . " Custom: {$video->has_custom} Code: {$video->has_code} - {$video->name}<br />";
//		}
//		echo "</pre>";

		// No videos left in filters means they've scored all active videos (or there are no videos to score)
		if(count($filtered) == 0) {
			return redirect()->route('video.judge.index')->with('message', 'You cannot judge any more videos.');
		}

		// Sort videos to determine which one gets scored next
		// In this code minus is used as a stand in for the non-existent spaceship operator <=>
		// Logic:
		//   Top priority is custom parts.  Sort Descending.  Ignore if user doesn't score custom.
		//   Second priority is code.  Sort Descending.  Ignore if user doesn't score code.
		//   Third Priority is everything else.  Sort by count of scores, ascending.
		$sorted = $filtered->sort( function ($a, $b) use ($judge_compute, $judge_custom){
				// Has Custom?
				$has_custom = $b->has_custom - $a->has_custom;
				if($has_custom == 0 OR !$judge_custom) {
					// Custom is the same, check code
					$has_code = $b->has_code - $a->has_code;
					if($has_code == 0 OR !$judge_compute) {
						// Code is the same, check count
						return count($a->scores) - count($b->scores);
					} else {
						return $has_code;
					}
				} else {
					// Custom Differs
					return $has_custom;
				}
			});

//		echo "Sorted:<br/><pre>";
//		foreach($sorted as $video) {
//			echo $video->id . " - scores: " . count($video->scores) . " Custom: {$video->has_custom} Code: {$video->has_code} - {$video->name}<br />";
//		}
//		echo "</pre>";
//		exit;

		// The top item on the list gets scored next
		$video = $sorted->first();

		//return View::make('video_scores.create', compact('video', 'types'));
		return redirect()->route('video.judge.score', [ 'video_id' => $video->id, 'no-cache' => microtime() ] );

	}

	// Score a Specific Video combination
	public function score(Request $req, $video_id) {
		//Breadcrumbs::addCrumb('Score Video', 'score');
		View::share('title', 'Score Video');

		$video = Video::with('vid_division.competition', 'comments')->find($video_id);
		if(empty($video)) {
			// Invalid video
			return redirect()->route('video.judge.index')
							->with('message', "Invalid video id '$video_id'.  Video no longer exists or another error occured.");
		}
//dd(DB::getQueryLog());
		// We always score general
		$video_types = [ VideoType::General ];

		// User Custom Parts
		if($req->hasCookie('judge_custom')) {
			$video_types[] = VideoType::Custom;
		}

		// User Computational Thinking
		if($req->hasCookie('judge_compute')) {
			$video_types[] = VideoType::Compute;
		}

		// Ensure we have not already scored this video
		$score_count = Video_scores::where('video_id', $video_id)
								   ->where('judge_id', Auth::user()->id)
								   ->count();

		// If there are any scores, go to edit mode
		if($score_count > 0) {
			return redirect()->route('video.judge.edit', [ 'video_id' => $video_id ])
							->with('message', 'You already scored this video.  Switched to Edit Mode.');
		}

		// Competition id to filter rubric
		$vid_competition_id = $video->vid_division->competition->id;

		$types = Vid_score_type::with( [ 'Rubric' => function($q) use ($vid_competition_id) {
			return $q->where('vid_competition_id', $vid_competition_id);
		}])->whereIn('group', $video_types)->get();

		return View::make('video_scores.create', compact('video', 'types'));
	}

	/**
	 * Show the form for creating a new Video_scores
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		return View::make('video_scores.create');
	}

	// Take an individual raw score from the form and turn it into
	// something which can be used to create or update
	// a score record
	private function calculate_score($type, $score)
	{
		$group = Vid_score_type::whereId($type)->first()->group;
		$total = 0;
		$score_count = count($score);
		// Loop through s1..s20, totalling or creating the index
		for($i = 1; $i < 11; $i++)
		{
			$index = 's' . $i;
			if(array_key_exists($index, $score)) {
				$total += $score[$index];
			} else {
				$score[$index] = 0;
			}
		}
		$score['total'] = $total;
		$score['average'] = $total / $score_count;
		$score['norm_avg'] = $score['average'];
		$score['vid_score_type_id'] = $type;
		$score['score_group'] = $group;

		return $score;
	}

	/**
	 * Store a newly created video_scores.in storage.
	 *
	 * @param Request $req
	 * @param $video_id
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $req, $video_id)
	{
		$input = $req->except([ 'report_problem','comment' ]);
		$video = Video::find($video_id);

		foreach($input['scores'] as $type => $score) {
			$score = $this->calculate_score($type, $score);
			$score['video_id'] = $video_id;
			$score['vid_division_id'] = $video->vid_division_id;
			$score['judge_id'] = Auth::user()->id;
			Video_scores::create($score);
		}

		// Deal with problem reports
		if($req->has('report_problem') AND $req->has('comment')) {
			$video_comment['video_id'] = $video->id;
			$video_comment['judge_id'] = Auth::user()->id;
			$video_comment['comment'] = $req->input('comment', '--No Comment Entered--');

			Video_comment::create($video_comment);

			// Flag the video for Review
			$video->flag = VideoFlag::Review;
			$video->save();

			// TODO:  E-mail admin to let them know a video has been flagged
		}


		return redirect()->route('video.judge.index');
	}

	/**
	 * Display the specified video_scores.
	 *
	 * @param $video_id
	 * @return \Illuminate\Http\Response
	 */
	public function show($video_id)
	{
		//Breadcrumbs::addCrumb('Show Video', 'score');
		View::share('title', 'Show Video');

		$video = Video::find($video_id);
		if(empty($video)) {
			// Invalid video
			return redirect()->route('video.judge.index')
							->with('message', "Invalid video id '$video_id'.  Video no longer exists or another error occured.");
		}

		return View::make('video_scores.show', compact('video'));
	}

	/**
	 * Show the form for editing the specified video_scores.
	 *
	 * @param $video_id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($video_id)
	{
		//Breadcrumbs::addCrumb('Edit Score', 'edit');
		View::share('title', 'Edit Video Score');
		$video = Video::with('vid_division.competition', 'comments')->find($video_id);

		if(empty($video)) {
			// Invalid video
			return redirect()->route('video.judge.index')
							->with('message', "Invalid video id '$video_id'.  Video no longer exists or another error occured.");
		}

		$scores = Video_scores::where('video_id', $video_id)
								   ->where('judge_id', Auth::user()->id)
								   ->get();
		if(count($scores)==0) {
			return redirect()->route('video.judge.index')
							->with('message', 'No scores to edit for this video.');
		}
		//dd($scores);

		$groups = [ ];
		$groups = array_keys($scores->pluck('score_group', 'score_group')->all());

		//$missing_groups = array_diff([ VideoType::Compute, VideoType::General, VideoType::Custom ], $groups);

		// Competition id to filter rubric
		$vid_competition_id = $video->vid_division->competition->id;

		$types = Vid_score_type::with( [ 'Rubric' => function($q) use ($vid_competition_id) {
			return $q->where('vid_competition_id', $vid_competition_id);
		}])->whereIn('group', $groups)->get();

		$video_scores = [];
		foreach($scores as $score) {
			$video_scores[$score->vid_score_type_id]['id'] = $score->id;
			$video_scores[$score->vid_score_type_id]['s1'] = $score->s1;
			$video_scores[$score->vid_score_type_id]['s2'] = $score->s2;
			$video_scores[$score->vid_score_type_id]['s3'] = $score->s3;
			$video_scores[$score->vid_score_type_id]['s4'] = $score->s4;
			$video_scores[$score->vid_score_type_id]['s5'] = $score->s5;
			$video_scores[$score->vid_score_type_id]['s6'] = $score->s6;
			$video_scores[$score->vid_score_type_id]['s7'] = $score->s7;
			$video_scores[$score->vid_score_type_id]['s8'] = $score->s8;
			$video_scores[$score->vid_score_type_id]['s9'] = $score->s9;
			$video_scores[$score->vid_score_type_id]['s10'] = $score->s10;
		}

		return View::make('video_scores.edit', compact('video', 'video_scores', 'types'))
						->with('group_names', $this->group_names);
	}

	/**
	 * Update the specified video_scores.in storage.
	 *
	 * @param Request $req
	 * @param $video_id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $req, $video_id)
	{
		$input = $req->all();
		$video = Video::with('division','division.competition','division.competition.user')
			->find($video_id);


		foreach($input['scores'] as $type => $score) {
			$score = $this->calculate_score($type, $score);
			$score['video_id'] = $video_id;
			$score['vid_division_id'] = $video->vid_division_id;
			$score['judge_id'] = Auth::user()->id;
			$this_score = Video_scores::find($score['id']);
			$this_score->update($score);
		}

		// Deal with problem reports
		if($req->has('report_problem') AND $req->has('comment')) {
			$video_comment['video_id'] = $video->id;
			$video_comment['judge_id'] = Auth::user()->id;
			$video_comment['comment'] = $req->input('comment', '--No Comment Entered--');

			Video_comment::create($video_comment);

			// Flag the video for Review
			$video->flag = VideoFlag::Review;
			$video->save();

			$coordinator = $video->division->competition->user;
			if($coordinator) {
				Mail::to($coordinator)
					->queue(
						new VideoReport([
							'video' => $video,
							'comment' => $video_comment['comment']
						])
					);
			}
		}

		return redirect()->route('video.judge.index');
	}

	// Clear scores for a specific video and user
	public function clear_scores($video_id, $judge_id) {
		// Only owners may clear their own scores, or admins
		if(Roles::isAdmin() OR $judge_id == Auth::user()->id) {
			Video_scores::where('video_id', $video_id)->where('judge_id', $judge_id)->delete();

			return redirect()->route('video.judge.index')->with('message', 'Score Cleared');

		} else {
			return redirect()->route('video.judge.index')->with('message','You do not have permission to clear these scores');
		}

	}

	/**
	 * Remove the specified video_scores.from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		Video_scores::destroy($id);

		return redirect()->route('video_scores.index');
	}

}
