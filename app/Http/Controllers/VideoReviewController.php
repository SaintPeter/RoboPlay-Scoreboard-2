<?php

namespace App\Http\Controllers;

use DB;
use View;

use App\Models\ {
	Video_review_categories,
	CompYear
};
use Illuminate\Http\Request;

class VideoReviewController extends Controller
{
    public function index($year = 0) {
	    $year = CompYear::yearOrMostRecent($year);
	    $yearList = json_encode(CompYear::orderBy('year')->pluck('year'));
	    $problemList = Video_review_categories::with('details')
		    ->orderBy('order')
		    ->get()
		    ->toJson();

    return View::make('video_review.index')
		    ->with(compact('year','yearList','problemList'));

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
}
