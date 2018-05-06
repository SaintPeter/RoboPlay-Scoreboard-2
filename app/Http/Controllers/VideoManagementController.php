<?php

namespace App\Http\Controllers;

use DB;
use JpGraph\JpGraph;
use URL;
use View;
use Session;
use Carbon\Carbon;
//use JpGraph\JpGraph;
use Illuminate\Http\Request;

use App\Enums\ {
	VideoFlag,
	UserTypes
};

use App\Models\{
	Rubric, User, Video, Video_comment, Video_scores, Vid_score_type, Vid_competition
};

class VideoManagementController extends Controller {

	public function index($year = null)
	{
		//Breadcrumbs::addCrumb('Manage Scores');

		$year = is_null($year) ? Session::get('year', false) : intval($year);

		$scores_query = Video_scores::with('division', 'division.competition', 'user', 'video')
							->orderBy('total', 'desc');
		if($year) {
			$scores_query = $scores_query->where(DB::raw("year(created_at)"), $year);
		}
		$video_scores = $scores_query->get();

		$videos = [];

		$types = Vid_score_type::orderBy('id')->pluck('name', 'id')->all();
		$blank = array_combine(array_keys($types), array_fill(0, count($types), '-'));
		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->user->name][$score->video->name] = $blank;
			$videos[$score->division->longname()][$score->user->name][$score->video->name]['video_id'] = $score->video_id;
			$videos[$score->division->longname()][$score->user->name][$score->video->name]['user_id'] = $score->user_id;
		}

		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->user->name][$score->video->name][$score->vid_score_type_id] = $score->total;
		}
		//dd(DB::getQueryLog());
		View::share('title', 'Manage Scores');
		return View::make('video_scores.manage.index', compact('videos','types','year'));

	}

	public function summary($year = null)
	{
		//Breadcrumbs::addCrumb('Scoring Summary');
		View::share('title', 'Scoring Summary');

		$year = is_null($year) ? Session::get('year', false) : intval($year);

		// Videos with score count
		if($year) {
			$videos = Video::with('scores', 'vid_division', 'vid_division.competition')->where(DB::raw("year(created_at)"), $year)->get();
		} else {
			$videos = Video::with('scores', 'vid_division', 'vid_division.competition')->get();
		}

		foreach($videos as $video) {
			$output[$video->vid_division->competition->name][$video->vid_division->name][] = $video;
		}

		return View::make('video_scores.manage.summary', compact('output','year'));
	}

	/**
	 * Display information about individual judges
	 * as well as overall summary info
	 *
	 * @param null $year
	 * @return \Illuminate\Contracts\View\View
	 */
	public function judge_performance($year = null)
	{
		View::share('title', 'Judge Performance');

		$year = intval($year) OR Session::get('year', false);

		// Users Scoring Count
		$user_list = User::with( [ 'video_scores' => function($q) use ($year) {
				if($year) {
					return $q->where(DB::raw("year(created_at)"), $year);
				} else {
					return $q;
				}
			} ] )->where('roles', '&', UserTypes::Judge)->get();

		// After 2017 there is a "theme" entry in general scores
		$general_divisor = 3;
		if($year > 2017) {
			$general_divisor = 4;
		}

		$user_score_count = [];
		foreach($user_list as $user) {
			$user_score_count[$user->name] = [ 1 => 0, 2 => 0, 3 => 0, 'total' => 0 ];
			if(count($user->video_scores)) {
				$user_score_count[$user->name][1] = $user->video_scores->reduce(function($count, $score) { return ($score->score_group == 1) ? $count + 1 : $count; }, 0) / $general_divisor;
				$user_score_count[$user->name][2] = $user->video_scores->reduce(function($count, $score) { return ($score->score_group == 2) ? $count + 1 : $count; }, 0);
				$user_score_count[$user->name][3] = $user->video_scores->reduce(function($count, $score) { return ($score->score_group == 3) ? $count + 1 : $count; }, 0);
				$user_score_count[$user->name]['total'] = array_sum($user_score_count[$user->name]);
			}
		}

		uasort($user_score_count, function($a, $b) {
				return $b['total'] - $a['total'];
		});

		return View::make('video_scores.manage.judge_performance', compact('user_score_count', 'year'));
	}

	// Process the deletion of scores
	// select[user_id] = [ video_id1, video_id2, . . . ]
	// types = Score Group(s) = 1/2/3/all
	public function process()
	{
		$select = $req->input('select');
		$types = $req->input('types');

		switch($types) {
			case 1:
				$groups = [ 1 ];
				break;
			case 2:
				$groups = [ 2 ];
				break;
			case 3:
				$groups = [ 3 ];
				break;
			case 'all':
				$groups = [ 1, 2, 3 ];
				break;
			default:
				return redirect()->to(URL::previous())->with('message', 'No Score Type Selected');
		}

		$affectedRows = 0;
		foreach($select as $user_id => $video_list) {
			$affectedRows += Video_scores::where('user_id', $user_id)
										 ->whereIn('video_id', $video_list)
										 ->whereIn('score_group', $groups)
										 ->delete();
		}

		return redirect()->to(URL::previous())
					    ->with('message', "Deleted $affectedRows scores");
	}

	// Displays score summary sorted by video then by user
	public function by_video($year = null)
	{
		//Breadcrumbs::addCrumb('Scores By Video');
		$video_scores = Video_scores::with('division', 'division.competition', 'user', 'video')
							->orderBy('total', 'desc');
		if($year) {
			$video_scores = $video_scores->where(DB::raw("year(created_at)"), $year)->get();
		} else {
			$video_scores = $video_scores->get();
		}

		$videos = [];
		$types = Vid_score_type::orderBy('id')->pluck('name', 'id')->all();
		$blank = array_combine(array_keys($types), array_fill(0, count($types), '-'));
		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->video->name][$score->user->name] = $blank;
			$videos[$score->division->longname()][$score->video->name][$score->user->name]['video_id'] = $score->video_id;
			$videos[$score->division->longname()][$score->video->name][$score->user->name]['user_id'] = $score->user_id;
		}

		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->video->name][$score->user->name][$score->vid_score_type_id] = $score->total;
		}
		//dd(DB::getQueryLog());
		View::share('title', 'Manage Scores');
		return View::make('video_scores.manage.by_video', compact('videos', 'types', 'year'));

	}

	public function scores_csv($year = null) {
		$video_scores = Video_scores::with('division', 'division.competition', 'user')
							->orderBy('total', 'desc');
		if($year) {
			$video_scores = $video_scores->where(DB::raw("year(created_at)"), intval($year))->get();
		} else {
			$video_scores = $video_scores->get();
		}

		$videos = [];
		//dd(DB::getQueryLog());
		$types = Vid_score_type::orderBy('id')->pluck('name', 'id')->all();
		$blank = array_combine(array_keys($types), array_fill(0, count($types), ''));
		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->video->name][$score->user->name] = $blank;
			$videos[$score->division->longname()][$score->video->name][$score->user->name]['video_id'] = $score->video_id;
			$videos[$score->division->longname()][$score->video->name][$score->user->name]['user_id'] = $score->user_id;
		}

		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->video->name][$score->user->name][$score->vid_score_type_id] = $score->total;
		}

		$content = 'Division,Video Name,ID,User Name,' . join(',', $types) . "\n";

		$line = [];
		foreach($videos as $video_division => $video_list) {
			foreach($video_list as $video_name => $user_list) {
				foreach($user_list as $user_name => $scores) {
					$line[] = $video_division;
					$line[] = $video_name;
					$line[] = $scores['video_id'];
					$line[] = $user_name;
					foreach($types as $index => $type) {
						$line[] = $scores[$index];
					}
					$content .= '"' . join('","', $line) . "\"\n";
					$line = [];
				}
			}
		}

		//dd($content);

		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="video_scores.csv'
		));
	}

	public function reported_videos($year = null) {
		//Breadcrumbs::addCrumb('Reported Videos');
		View::share('title', 'Reported Videos');
		$comments_reported = Video_comment::whereHas('video', function($q) use ($year) {
					if($year) {
						$q = $q->where('year', $year);
					}
					return $q->where('flag', VideoFlag::Review);
				} )->with('video','user')->get();

		$comments_resolved = Video_comment::whereHas('video', function($q) use ($year) {
					if($year) {
						$q = $q->where('year', $year);
					}
					return $q->where('flag', '<>', VideoFlag::Review);
				} )->with('video','user')->get();

		return View::make('video_scores.manage.reported_videos', compact('comments_reported', 'comments_resolved','year'));
	}

	/**
	 * Save a report to the DB
	 * @param Request $req
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function process_report(Request $req) {
		if($req->has('absolve')) {
			$comment = Video_comment::with('video')->find($req->input('absolve'));
			$comment->resolution = $req->input('resolution_' . $req->absolve, 'No Resolution Given');
			$comment->save();
			$comment->video->flag = VideoFlag::Normal;
			$comment->video->save();
		} elseif ($req->has('dq')) {
			$comment = Video_comment::with('video')->find($req->input('dq'));
			$comment->resolution = $req->input('resolution_' . $req->dq, 'No Resolution Given');
			$comment->save();
			$comment->video->flag = VideoFlag::Disqualified;
			$comment->video->save();
		}
		return redirect()->to(URL::previous());
	}

	public function unresolve($comment_id) {
		$comment = Video_comment::with('video')->find($comment_id);
		$comment->video->flag = VideoFlag::Review;
		$comment->video->save();
		return redirect()->to(URL::previous());
	}

	public function graph_video_scoring($year = null) {
	    $year = is_null($year) ? Session::get('year', false) : intval($year);

	    // Get the competition start/end dates
	    $comp = Vid_competition::where(DB::raw("year(event_start)"), $year)->first();

        // Calculate day delta
	    if(Carbon::now()->gt($comp->event_end)) {
	        $delta_day = "Final";
	    } else {
	        $delta_day = "Day " . Carbon::now()->addDay()->diffInDays($comp->event_start);
	    }

		// Videos with score count
		if($year) {
			$videos = Video::where('flag', 0)->with('scores')->where(DB::raw("year(created_at)"), $year)->get();
		} else {
			$videos = Video::where('flag', 0)->with('scores')->get();
		}


        // Get counts for all score types
        $max = 0;
		foreach($videos as $video) {
		    $score = $video->all_scores_count();
			$scores[] = $score;
			$max = max(max(array_values($score)), $max);
		}
		$max += 3;

		// Create blank arrays to fill in
		$general = array_fill(0, $max, 0);
		$code    = array_fill(0, $max, 0);

		// Fill in histogram
		foreach($scores as $score) {
		    $general[$score['general']]++;
		    $code[$score['compute']]++;
		}

        // Generate Graph

	    JpGraph::load();
	    JpGraph::module('bar');

	     // Width and height of the graph
        $width = 575; $height = 350;

         // Create a graph instance
        $graph = new \Graph($width,$height);
        $graph->ClearTheme();

        // Give room on the edges
        $graph->SetMargin(50,30,50,40);

        // Specify what scale we want to use,
        $graph->SetScale('textlin');

        // Setup a title for the graph
        $graph->title->Set("Video Scoring - $delta_day");
        $graph->title->SetFont(FF_ARIAL,FS_BOLD,14);
        $graph->title->SetMargin(15);

        // Setup titles and X-axis labels
        $graph->xaxis->SetTitle('Number of Scores', 'center');
        $graph->xaxis->SetTickLabels(array_keys($general));


        // Setup Y-axis title
        $graph->yaxis->title->Set('Number of Videos');

             // Create the bar graph
        $bp1 = new \BarPlot($general);
        $bp2 = new \BarPlot($code);

        // Style Bars
        $bp1->SetFillColor([79, 129, 189]); // Blue
        $bp2->SetFillColor([155, 187, 89]); // Green
        $bp1->SetColor('white@1.0');
        $bp2->SetColor('white@1.0');

        // Legend
        $bp1->SetLegend('General');
        $bp2->SetLegend('Code');

        // width
        $bp1->SetWidth(0.3);
        $bp2->SetWidth(0.3);

        $acc = new \GroupBarPlot( [$bp1, $bp2] );
        // Add the plot to the graph
        $graph->Add($acc);


        // Display the graph
        $graph->Stroke();
	}

	public function graph_judge_scoring($year = 2015) {
        $year = intval($year);

        // Get the competition start/end dates
	    $comp = Vid_competition::where(DB::raw("year(event_start)"), $year)->first();

        // Calculate day delta
	    if(Carbon::now()->gt($comp->event_end)) {
	        $delta_day = "Final";
	    } else {
	        $delta_day = "Day " . Carbon::now()->addDay()->diffInDays($comp->event_start);
	    }

	    $video_count = Video::where('flag', 0)->where(DB::raw("year(created_at)"), $year)->count();

		// Users Scoring Count
		$user_list = User::with( [ 'video_scores' => function($q) use ($year) {
				if($year) {
					return $q->where(DB::raw("year(created_at)"), $year);
				} else {
					return $q;
				}
			} ] )->where('roles', '&', UserTypes::Judge)->get();

		// After 2017 there is a "theme" entry in general scores
		$general_divisor = 3;
		if($year > 2017) {
			$general_divisor = 4;
		}

		$user_score_count = [];
		foreach($user_list as $user) {
			if(count($user->video_scores)) {
				$user_score_count[$user->id]['general'] = $user->video_scores->reduce(function($count, $score)
					{ return ($score->score_group == 1) ? $count + 1 : $count; }, 0) / $general_divisor;
				$user_score_count[$user->id]['code'] = $user->video_scores->reduce(function($count, $score)
				{ return ($score->score_group == 3) ? $count + 1 : $count; }, 0);
			}
		}

        // Make Bins
        $bins = [ 0 ];
        for($i = 5; $i < $video_count; $i += 5) {
            $bins[] = $i;
        }
        $bins[] = $video_count;

        $general = array_fill(0,count($bins),0);
        $code = array_fill(0,count($bins),0);

		foreach($user_score_count as $counts) {
		    // General
		    foreach($bins as $index => $bin) {
		        if($counts['general'] <= $bin) {
		            $general[$index]++;
		            break;
		        }
		    }
		    // Code
		    foreach($bins as $index => $bin) {
		        if($counts['code'] <= $bin) {
		            $code[$index]++;
		            break;
		        }
		    }
		}

        //dd($general, $code);

        // Generate Graph

	    JpGraph::load();
	    JpGraph::module('bar');

	     // Width and height of the graph
        $width = 575; $height = 350;

        // Create a graph instance
        $graph = new \Graph($width,$height);
        $graph->ClearTheme();

        // Give room on the edges
        $graph->SetMargin(50,30,50,40);

        // Specify what scale we want to use,
        $graph->SetScale('textlin');

        // Setup a title for the graph
        $graph->title->Set("User Performace - $delta_day");
        $graph->title->SetFont(FF_ARIAL,FS_BOLD,14);
        $graph->title->SetMargin(15);

        // Setup titles and X-axis labels
        $graph->xaxis->SetTitle('Videos Scored', 'center');
        $graph->xaxis->SetTickLabels($bins);


        // Setup Y-axis title
        $graph->yaxis->title->Set('Number of Users');

        // Create the bar graph
        $bp1 = new \BarPlot($general);
        $bp2 = new \BarPlot($code);

        // Style Bars
        $bp1->SetFillColor([79, 129, 189]); // Blue
        $bp2->SetFillColor([155, 187, 89]); // Green
        $bp1->SetColor('white@1.0');
        $bp2->SetColor('white@1.0');

        // Legend
        $bp1->SetLegend('General');
        $bp2->SetLegend('Code');

        // width
        $bp1->SetWidth(0.1);
        $bp2->SetWidth(0.1);

        $acc = new \GroupBarPlot( [$bp1, $bp2] );
        // Add the plot to the graph
        $graph->Add($acc);


        // Display the graph
        $graph->Stroke();
	}

	public function graphs($year = null)
	{
	    //Breadcrumbs::addCrumb('Graphs');
		View::share('title', 'Graphs');
	    $year = is_null($year) ? Session::get('year', false) : intval($year);

	    return View::make('video_scores.manage.graphs', compact('year'));
	}

	public function rubric($competition_id = null, $edit = null) {
		View::share('title', 'Rubric Management');

		$hasScores = Vid_competition::has('scores')->where('id',$competition_id)->count();

		$vid_competitions = [ '0' => '-- Select Competition --']
			+ Vid_competition::has('rubric')
				->orderBy('id', 'desc')
				->pluck('name', 'id')
				->all();

		$dest_competitions = Vid_competition::doesntHave('rubric')->pluck('name', 'id')->all();

		$vid_score_type = [];
		if($competition_id) {
			$vid_score_type = Vid_score_type::with(['rubric' => function($q) use ($competition_id) {
				return $q->where('vid_competition_id', $competition_id);
			}])->get();
		}

		return View::make('admin.rubric.index')
			->with(compact('competition_id','edit', 'vid_competitions','dest_competitions','vid_score_type','hasScores'));
	}

	public function rubric_view($competition_id) {
		$vid_score_type = Vid_score_type::with(['rubric' => function($q) use ($competition_id) {
			return $q->where('vid_competition_id', $competition_id);
		}])->get();

		return View::make('admin.rubric.partial.view',compact('vid_score_type', 'competition_id'));
	}

	public function rubric_edit($competition_id) {
		$hasScores = Vid_competition::has('scores')->where('id',$competition_id)->count();

		$vid_score_type = Vid_score_type::with(['rubric' => function($q) use ($competition_id) {
			return $q->where('vid_competition_id', $competition_id);
		}])->get();

		return View::make('admin.rubric.partial.edit',compact('vid_score_type', 'competition_id','hasScores'));
	}

	public function rubric_blank_row(Request $req, $competition_id, $vid_score_type_id, $rowId ) {
		if(!$competition_id || !$vid_score_type_id || !$rowId) {
			return response("Missing Parameter",400);
		}

		return View::make('admin.rubric.partial.blank_row', compact('competition_id','vid_score_type_id','rowId'));

	}

	public function rubric_save(Request $req, $competition_id) {
		$rubrics = $req->rubric;
		$newCount = 0;
		$updateCount = 0;
		$deleteCount = 0;
		foreach($rubrics as $id => $rubric) {
			if($rubric['delete']) {
				Rubric::destroy($id);
				$deleteCount++;
			} else {
				if ($rubric['delta']) {
					$rubric['element'] = 's' . $rubric['order'];
					if ($rubric['new']) {
						Rubric::create($rubric);
						$newCount++;
					} else {
						$updateRubric = Rubric::find($id);
						$updateRubric->update($rubric);
						$updateCount++;
					}
				}
			}
		}
		return redirect()->route('rubric.view', [ $competition_id ])
			->with(['message' => "$newCount Elements Created, $updateCount Elements Updated, $deleteCount Elements Deleted"]);
	}

	public function rubric_copy_to(Request $req, $comp_id, $dest_id) {
		if($comp_id == $dest_id) {
			return redirect()->route('rubric.index')
				->with(['message' => 'Source and destination cannot be identical']);
		}
		$inputRubric = Rubric::where('vid_competition_id',$comp_id)->get()->except(['id'])->toArray();

		$rowCount = 0;
		$newRubric = array_map(function($row) use ($dest_id, &$rowCount) {
			unset($row['id']);
			$row['vid_competition_id'] = $dest_id;
			$rowCount++;
			return $row;
		}, $inputRubric);

		Rubric::insert($newRubric);

		return redirect()->route('rubric.view', [ $dest_id ])->with(['message' => "$rowCount Rubric Elements Copied"]);
	}

}