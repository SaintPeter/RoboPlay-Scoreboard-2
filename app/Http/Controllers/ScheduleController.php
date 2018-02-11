<?php

namespace App\Http\Controllers;

use View;
use Session;
use Validator;
use Illuminate\Http\Request;

use App\ {
    Models\Schedule
};
use Carbon\Carbon;

class ScheduleController extends Controller {

	public function __construct()
	{
		parent::__construct();
		//Breadcrumbs::addCrumb('Schedule', 'math_divisions');
	}


	/**
	 * Display a listing of the resource.
	 * GET /schedule
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
	    View::share('title', 'Schedule Editor');

        $schedule =  Schedule::orderBy('start')->get();

	    Carbon::setToStringFormat('g:i:s a');

	    if(Session::has('errors')) {
	        ddd(Session::get('errors'));
	    }

		return View::make('schedule.index', compact('schedule'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /schedule/{id}
	 *
	 * @return Redirect to Index
	 */
	public function update()
	{
		$new_schedule = $req->input('schedule', []);
		$current_schedule = Schedule::orderBy('start')->get();

		$keys = array_keys($new_schedule);
        $errors = false;

		foreach($keys as $i => $key) {
		    $update_schedule = $current_schedule->find($new_schedule[$key]['id']);

		    // Create a new model if it doesn't exist
		    if(!$update_schedule) {
		        $update_schedule = new Schedule;
		        $new_schedule[$key]['id'] = $key;
		    }

		    // End time is always the next element's start time, unless it is the last
		    if(isset($keys[$i+1])) {
		        $new_schedule[$key]['end'] = $new_schedule[$keys[$i+1]]['start'];
		    } else {
		        $new_schedule[$key]['end'] = $new_schedule[$key]['start'];
		    }

		    // Validate
		    $scheduleErrors = Validator::make($new_schedule[$key], Schedule::$rules);

		    if($scheduleErrors->passes()) {
		        $update_schedule->update($new_schedule[$key]);
		    } else {
		        $error_list[$key] = $scheduleErrors->messages()->all();
		        $errors = true;
		    }
		}
//ddd($new_schedule);
		if($errors) {
		    return redirect()->route('schedule.index')
		                   ->withErrors( [ 'schedule' => $new_schedule ] )
		                   ->with( [ 'message' => "Errors Occured" ]);
		}

		return redirect()->route('schedule.index')->with(['message' => 'Schedule Updated']);
	}
}