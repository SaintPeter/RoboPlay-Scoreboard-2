<?php

namespace App\Http\Controllers;

use DB;
use View;

use App\Models\{
	Video, CompYear, Video_review_categories, Video_review_details
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
	    $problemDetailList = Video_review_details::all()->toJson();

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

}
