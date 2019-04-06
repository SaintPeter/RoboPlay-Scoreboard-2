<?php

namespace App\Http\Controllers;

use App\Mail\VideoReviewNotification;
use DB;
use Mail;
use View;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\{
	Video, CompYear, Video_review_categories, Video_review_details, Video_review_problems, Invoices
};

use App\Enums\VideoReviewStatus;
use App\Mail\VideoDisqualification;

class VideoReviewController extends Controller
{
    public function index($year = 0) {
    	if(!$year) {
		    $year = CompYear::yearOrMostRecent($year);
		    return redirect()->route('video_review', [ $year ]);
	    }

	    $yearList = json_encode(CompYear::orderBy('year')->pluck('year'));
	    $problemList = Video_review_categories::with('details')
		    ->orderBy('order')
		    ->get()
		    ->toJson();
	    $problemDetailList = Video_review_details::all()->keyBy('id')->toJson();

    return View::make('video_review.index')
		    ->with(compact('year','yearList','problemList', 'problemDetailList'));

    }

    public function review_status($year) {
	    $reviewStatus = DB::table('videos')
		    ->select(DB::raw(
		    	'year, ' .
			    'SUM(review_status = 0) AS unreviewed,' .
			    'SUM(review_status = 1) AS reviewed,' .
			    'SUM(review_status = 2) AS disqualified,' .
			    'SUM(review_status = 3) AS passed'))
		    ->where('year','=', $year)
		    ->groupBy('year')
		    ->get();

	    return response()->json($reviewStatus->keyBy('year'));
    }

    public function get_next($year) {
	    try {
		    $random_video = DB::table('videos')
			    ->where([
				    ['year', '=', $year],
				    ['review_status', '=', VideoReviewStatus::Unreviewed]
			    ])
			    ->select('id')->get()->random();
	    } catch (\InvalidArgumentException $ex) {
		    return response()->json([
			    'error' => True,
			    'message' => 'No More Videos to Review'
		    ]);
	    }

	    return response()->json([
	    	'error' => False,
		    'id' => $random_video->id
	    ]);
    }

    public function fetch_video($year, $id) {
    	try {
		    $video = Video::with('problems', 'files')
			    ->findOrFail($id)
			    ->toArray();
	    } catch(ModelNotFoundException $ex) {
    		return response()->json([
			    'error' => True,
		        'message' => "Cannot Find Video"
		    ]);
	    }

    	$video['error'] = False;
    	$video['message'] = "";

    	return response()->json($video);
    }

    public function save_problems(Request $req, $video_id) {
    	$problems = collect($req->input('problems', []));
	    $videoDetail = Video_review_details::with('category')
		    ->orderBy('order')
		    ->get();

    	$stats = [
    		'updated' => 0,
	        'new' => 0
	    ];

    	$problems->each(function ($problem) use ($video_id, $req, &$stats, $videoDetail) {
    		$problem['video_id'] = $video_id;
		    $problem['resolved'] = false;

		    $problem['order'] = $videoDetail->find($problem['video_review_details_id'])->order;

    		if(array_key_exists('id', $problem)) {
    			unset($problem['reviewer_id']);

			    Video_review_problems::find($problem['id'])->fill($problem)->save();
			    $stats['updated']++;
		    } else {
			    $problem['reviewer_id'] = $req->user()->id;
			    $problem['updated_at'] = Carbon::now();
			    $problem['created_at'] = Carbon::now();
    			Video_review_problems::create($problem);
			    $stats['new']++;
		    }
	    });

    	// Update review status plus owner
    	if($stats['updated'] || $stats['new']) {
		    Video::where('id','=',$video_id)
			    ->update([
				    'review_status' => VideoReviewStatus::Reviewed,
				    'reviewer_id'=> $req->user()->id
			    ]
		    );
	    }

    	// Send message to vid coordinator
        $coordinator = Video::with('division','division.competition','division.competition.user')
		    ->find($video_id)
	        ->division->competition->user;
    	if($coordinator) {
    		Mail::to($coordinator)
			    ->queue(new VideoReviewNotification($video_id));
	    }

    	return response()->json($stats);
    }

    // Update a video to show that it has been reviewed and by whom
    public function save_no_problems(Request $req, $id) {
		return response()->json([
			'result' => Video::where('id','=',$id)
				->update([
					'review_status' => VideoReviewStatus::Passed,
					'reviewer_id'=> $req->user()->id
					]
				)
		]);
    }

    // Mark a problem resolved
	public function resolve_problem(Request $req, $video_id, $problem_id) {
    	$video = Video::with('problems')->findOrFail($video_id);
    	$problem = $video->problems->find($problem_id);

    	if($problem) {
	        $problem->resolved = true;
	        $problem->resolver_id = $req->User()->id;
	        $problem->save();
	    }

    	return response('true');
	}

	// Send disqualification message
	public function send_dq($video_id) {
    	$invoice = Invoices::with('teacher','videos')->whereHas('videos', function($query) use ($video_id) {
    		return $query->where('id', $video_id);
	    })->first();

    	if($invoice) {
    		Mail::to($invoice->teacher)
			    ->queue(new VideoDisqualification($video_id));
    		$video = $invoice->videos->find($video_id);
    		if($video) {
    			$video->review_status = VideoReviewStatus::Disqualified;
    			$video->save();
		    }
    		return response('true');
	    } else {
    		return response('false');
	    }
	}

	// Change video review status
	public function set_review_status(Video $video, $status) {
    	$video->review_status = intval($status);
    	$video->save();
    	return response('true');
	}

    // List of videos reviewed by user with problems
	public function reviewed_videos($year, $id) {
    	$videos = Video::with('problems','problems.detail','reviewer')
	        ->where('reviewer_id','=', $id)
		    ->where('year','=', $year)
		    ->orderBy('review_status')
		    ->get()
		    ->toArray();

    	return response()->json($videos);
	}

	// List of all reviewed videos for a given year
	public function all_reviewed_videos($year) {
		$videos = Video::with('problems','problems.detail','reviewer')
			->where('review_status','>', VideoReviewStatus::Unreviewed)  // Exclude un-reviewed videos
			->where('year','=', $year)
			->orderBy('review_status')
			->get()
			->toArray();

		return response()->json($videos);
	}

}
