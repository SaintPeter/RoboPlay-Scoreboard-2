<?php

namespace App\Http\Controllers;

use DB;
use View;
use Carbon\Carbon;

use App\Models\{
	Video, CompYear, Video_review_categories, Video_review_details, Video_review_problems
};

use App\Enums\VideoReviewStatus;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    public function save_problems(Request $req, $id) {
    	$problems = collect($req->input('problems', []));
	    $videoDetail = Video_review_details::with('category')
		    ->orderBy('order')
		    ->get();


    	$stats = [
    		'updated' => 0,
	        'new' => 0
	    ];

    	$problems->each(function ($problem) use ($id, $req, &$stats, $videoDetail) {
    		$problem['video_id'] = $id;
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
		    Video::where('id','=',$id)
			    ->update([
				    'review_status' => VideoReviewStatus::Reviewed,
				    'reviewer_id'=> $req->user()->id
			    ]
		    );
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

}
