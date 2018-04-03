<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use View;
use Session;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use App\Models\ {
    Video,
    Student,
    Division,
    CompYear,
    Invoices,
    Ethnicity
};
class TeacherVideoController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		// Replaced by combined teacher interface
		return redirect()->route('teacher.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
        // Get the most recent competition year with video divisisons
	    $comp_year = CompYear::orderBy('year', 'desc')
	                         ->with([ 'vid_divisions' => function($q) {
										return $q->orderby('display_order');
									}])
							->first();

	    // Get most recent invoice for this user
		$invoice = Invoices::where('year', $comp_year->year)
	                       ->where('user_id', Auth::user()->id)
	                       ->with('school')
	                       ->first();

        $division_list = [ 0 => "- Select Division -" ] + $comp_year->vid_divisions->pluck('name', 'id')->all();

		// Student Setup
		$ethnicity_list = [ 0 => "- Select Ethnicity -" ] + Ethnicity::all()->pluck('name','id')->all();

		View::share('title', 'Create Video');
		return View::make('teacher.videos.create',compact('division_list', 'ethnicity_list', 'invoice'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $req
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $req)
	{
		$input = $req->except([ 'students' ]);
		$invoice = Invoices::find($req->input('invoice_id'));

		$input['year'] = $invoice->year;
		$input['teacher_id'] = $invoice->user_id;
		$input['school_id'] = $invoice->school_id;

		$students = $req->input('students');

		$videoErrors = Validator::make($input, Video::$rules);

		if ($videoErrors->passes())
		{
			if(!empty($students)) {
				$students_pass = true;
				foreach ($students as $index => $student) {
				 	$student_rules = Student::$rules;
				 	$studentErrors[$index] = Validator::make($student, $student_rules);
				 	 if($studentErrors[$index]->fails()) {
				 	 	$students_pass = false;
				 	 	$students[$index]['errors'] = $studentErrors[$index]->messages()->all();
				 	}
				}

				if($students_pass) {
					$newvideo = video::create($input);
					$sync_list = [];

					foreach ($students as $index => &$student) {
						$student['year'] = $invoice->year;
						if(array_key_exists('id', $student)) {
							$newStudent = Student::find($student['id']);
							$newStudent->update($student);
						} else {
							$student['school_id'] = $invoice->school_id;
							$student['teacher_id'] = $invoice->user_id;
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$newvideo->students()->sync($sync_list);
					return redirect()->route('teacher.videos.show', $newvideo->id);
				} else {
					return redirect()->route('teacher.videos.create')
						->withInput($req->except([ 'students' ]))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just create the team
				$video = Video::create($input);
				return redirect()->route('teacher.videos.show', $video->id);
			}
		}

		return redirect()->route('teacher.videos.create')
			->withInput($req->except([ 'students' ]))
			->with('students', $students)
			->withErrors($videoErrors)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		View::share('title', 'Video Preview');
		//Breadcrumbs::addCrumb('Manage Teams and Videos', 'teacher');
		//Breadcrumbs::addCrumb('Video Preview', 'teacher/videos/create');
		$video = Video::with('school')->findOrFail($id);

		return View::make('teacher.videos.show', compact('video'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		View::share('title', 'Edit Video');
		$video = Video::with('students')->find($id);

		// Get the most recent competition year with video divisisons
	    $comp_year = CompYear::orderBy('year', 'desc')
	                         ->with([ 'vid_divisions' => function($q) {
										return $q->orderby('display_order');
									}])
							->first();

		// Get most recent invoice for this user
		$invoice = Invoices::where('year', $comp_year->year)
			->where('user_id', Auth::user()->id)
			->with('school')
			->first();

        $division_list = [ 0 => "- Select Division -" ] + $comp_year->vid_divisions->pluck('name', 'id')->all();

		// Student Setup
		$ethnicity_list = [ 0 => "- Select Ethnicity -" ] + Ethnicity::all()->pluck('name','id')->all();
		if(!Session::has('students')) {
			// On first load we populate the form from the DB
			$students = $video->students;
		} else {
			// On subsequent loads or errors, use the sessions variable
			$students = [];
		}
		$index = -1;


		if (is_null($video))
		{
			return redirect()->route('teacher.index');
		}

		$divisions = Division::longname_array();

		return View::make('teacher.videos.edit',
					compact('video','students', 'division_list', 'ethnicity_list', 'index','invoice'))
				   ->with('divisions', $divisions);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $req
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $req, $id)
	{
		$input = $req->except([ '_method', 'students' ]);
		$invoice = Invoices::find($req->input('invoice_id'));

		$input['school_id'] = $invoice->school_id;
		$input['teacher_id'] = $invoice->user_id;
		$input['year'] = $invoice->year;

		$students = $req->input('students');

		$videoValidation = Validator::make($input, Video::$rules);

		if ($videoValidation->passes())
		{
			if(!empty($students)) {
				$students_pass = true;
				foreach ($students as $index => $student) {
				 	$student_rules = Student::$rules;
				 	$studentErrors[$index] = Validator::make($student, $student_rules);
				 	 if($studentErrors[$index]->fails()) {
				 	 	$students_pass = false;
				 	 	$students[$index]['errors'] = $studentErrors[$index]->messages()->all();
				 	}
				}

				if($students_pass) {
					$video = video::find($id);
					$video->audit = 0;
					$video->update($input);

					foreach ($students as $index => &$student) {
						$student['year'] = $invoice->year;
						if(array_key_exists('id', $student)) {
							$newStudent = Student::find($student['id']);
							$newStudent->update($student);
						} else {
							$student['teacher_id'] = $invoice->user_id;
							$student['school_id'] = $invoice->school_id;
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$video->students()->sync($sync_list);
					return redirect()->route('teacher.index');
				} else {
					return redirect()->route('teacher.videos.edit', $id)
						->withInput($req->except([ 'students' ]))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just update the video
				$video = video::find($id);
				$video->audit = 0;
				$video->update($input);
				return redirect()->route('teacher.index');
			}
		}

		return redirect()->route('teacher.videos.edit', $id)
			->withInput()
			->withErrors($videoValidation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		try {
			Video::find($id)->delete();
		} catch (\Exception $e) {
			// Ignore Exceptions
		}

		return redirect()->route('teacher.index');
	}
}
