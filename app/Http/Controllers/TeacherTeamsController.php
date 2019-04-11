<?php

namespace App\Http\Controllers;

use View;
use Auth;
use Session;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\ {
    Models\Team,
    Models\Student,
    Models\Division,
    Models\Invoices,
    Models\CompYear,
    Models\Ethnicity
};
class TeacherTeamsController extends Controller {

	public function index()
	{
	    // Replaced by combined interface
	    return redirect()->route('teacher.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//Breadcrumbs::addCrumb('Manage Teams and Videos', 'teacher');
		//Breadcrumbs::addCrumb('Add Video', 'create');

        // Get the most recent competition year with competition divisions
	    $comp_year = CompYear::orderBy('year', 'desc')
	                         ->with([ 'divisions' => function($q) {
										return $q->orderby('display_order');
									}])
							->first();

		$invoice = Invoices::where('year', $comp_year->year)
	                       ->where('user_id', Auth::user()->id)
	                       ->with('school')
	                       ->first();

	    $school = $invoice->school;

        $division_list[0] = "- Select Division -";
        foreach($comp_year->divisions as $division) {
            $division_list[$division->competition->name][$division->id] = $division->name;
        }

		// Ethnicity List Setup
		$ethnicity_list = [ 0 => "- Select Ethnicity -" ] + Ethnicity::all()->pluck('name','id')->all();

		View::share('title', 'Add Team - ' . $school->name);
        return View::make('teacher.teams.create', compact('school','ethnicity_list', 'division_list', 'invoice'));
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
		$input['school_id'] = $invoice->school_id;
		$input['year'] = $invoice->year;
		$input['teacher_id'] = $invoice->user_id;

		$students = $req->input('students');

		$teamErrors = Validator::make($input, Team::$rules);

		if ($teamErrors->passes())
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
					$newTeam = Team::create($input);
					$sync_list = [];

					foreach ($students as $index => &$student) {
						$student['year'] = Carbon::now()->year;
						if(array_key_exists('id', $student)) {
							$newStudent = Student::find($student['id']);
							$newStudent->update($student);
						} else {
							$student['school_id'] = $invoice->school_id;
							$student['teacher_id'] = $input['teacher_id'];
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$newTeam->students()->sync($sync_list);
					return redirect()->route('teacher.index');
				} else {
					return redirect()->route('teacher.teams.create')
						->withInput($req->except([ 'students' ]))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just create the team
				Team::create($input);
				return redirect()->route('teacher.index');
			}
		}

		return redirect()->route('teacher.teams.create')
			->withInput($req->except([ 'students' ]))
			->with('students', $students)
			->withErrors($teamErrors)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//Breadcrumbs::addCrumb('Manage Teams and Videos', 'teacher');
		//Breadcrumbs::addCrumb('Edit Team', $id);
		View::share('title', 'Edit Team');
		$team = Team::with('students')->find($id);

		// Get the most recent competition year with comptition divisisons
	    $comp_year = CompYear::orderBy('year', 'desc')
	                         ->with([ 'divisions' => function($q) {
										return $q->orderby('display_order');
									}, 'divisions.competition'])
							->first();

		$invoice = Invoices::where('year', $comp_year->year)
			->where('user_id', Auth::user()->id)
			->with('school')
			->first();

        $division_list[0] = "- Select Division -";
        foreach($comp_year->divisions as $division) {
            $division_list[$division->competition->name][$division->id] = $division->name;
        }

		// Student Setup
		$ethnicity_list = [ 0 => "- Select Ethnicity -" ] + Ethnicity::all()->pluck('name','id')->all();
		if(!Session::has('students')) {
			// On first load we populate the form from the DB
			$students = $team->students;
		} else {
			// On subsequent loads or errors, use the sessions variable
			$students = [];
		}
		$index = -1;


		if (is_null($team))
		{
			return redirect()->route('teacher.index');
		}

		$divisions = Division::longname_array();

		return View::make('teacher.teams.edit')
					->with(compact('team','students', 'division_list', 'ethnicity_list', 'index', 'invoice'))
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
		$input['year'] = $invoice->year;
		$input['teacher_id'] = $invoice->user_id;

		$students = $req->input('students');

		$teamValidation = Validator::make($input, Team::$rules);

		if ($teamValidation->passes())
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
					$team = Team::find($id);
					$input['audit'] = 0;
					$team->update($input);

					foreach ($students as $index => &$student) {
						$student['year'] = Carbon::now()->year;
						if(array_key_exists('id', $student)) {
							$newStudent = Student::find($student['id']);
							$newStudent->update($student);
						} else {
							$student['school_id'] = $invoice->school_id;
							$student['teacher_id'] = $input['teacher_id'];
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$team->students()->sync($sync_list);
					return redirect()->route('teacher.index');
				} else {
					return redirect()->route('teacher.teams.edit', $id)
						->withInput($req->except([ 'students' ]))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just update the team
				$team = Team::find($id);
				$input['audit'] = 0;
				$team->update($input);
				return redirect()->route('teacher.index');
			}

			return redirect()->route('teacher.index');
		}

		return redirect()->route('teacher.teams.edit', $id)
			->withInput()
			->withErrors($teamValidation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		Team::find($id)->delete();

		return redirect()->route('teacher.index');
	}

}
