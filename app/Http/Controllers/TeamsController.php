<?php

namespace App\Http\Controllers;

use View;
use Auth;
use Session;
use Validator;
use Illuminate\Http\Request;

use App\Models\ {
    Ethnicity,
    Invoices,
    Division,
    Team,
    Student
};
class TeamsController extends Controller {

	public function __construct()
	{
		parent::__construct();
		//Breadcrumbs::addCrumb('Teams', 'teams');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		// Selected year set in filters.php -> App::before()
		$year = Session::get('year', false);

		if($year) {
			$teams = Team::where('year', $year)->with('division', 'school', 'teacher', 'students')->get();
		} else {
			$teams = Team::with('division', 'school', 'teacher', 'students')
						->orderBy('year', 'desc')
						->get();
		}

		$division_list = Division::longname_array();

		View::share('title', 'Teams');
		return View::make('teams.index', compact('teams', 'division_list'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$invoices = Invoices::with([ 'school', 'user'])->orderBy('year','desc')->get();

		View::share('title', 'Add Team');
		$division_list = Division::longname_array(true);

		$invoice_list = $invoices->sort(function ($a, $b) {
			// Sort by last name
			$a_name = preg_split("/\s+/", $a->user->name);
			$b_name = preg_split("/\s+/", $b->user->name);
			return strcasecmp(array_pop($a_name), array_pop($b_name));
		})->reduce(function($acc, $invoice) {
			$acc[$invoice->year]['a' . $invoice->id]['teacher'] = $invoice->user->name . ' (' . $invoice->school->name . ')';
			$acc[$invoice->year]['a' . $invoice->id]['teacher_id'] = $invoice->user_id;
			return $acc;
		},[]);
		uksort($invoice_list, function($a, $b) { return $b <=> $a; });

		$yearList =  $invoices->pluck('year', 'year')->all();

		// Ethnicity List Setup
		$ethnicity_list = [ 0 => "- Select Ethnicity -" ] + Ethnicity::all()->pluck('name','id')->all();

		// Student Setup
		$students = [];

		View::share('index', 0);

		return View::make('teams.create')
				   ->with(compact('division_list', 'invoice_list', 'ethnicity_list','yearList','students'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $req
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $req)
	{
		$input = $req->except([ '_method', 'students' ]);
		$invoice = Invoices::find($req->input('invoice_id'));
		if(!$invoice) {
			return redirect()->route('teams.create')
				->withInput($req->except([ 'students' ]))
				->with('students', $req->input('students'))
				->withErrors(['message' => 'Teacher must be selected'])
				->with('message', 'There were validation errors.');
		}
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
						$student['year'] = $invoice->year;
						if(array_key_exists('id', $student)) {
							$newStudent = Student::find($student['id']);
							$newStudent->update($student);
						} else {
							$student['teacher_id'] = $req->input('teacher_id',Auth::user()->id);
							$student['school_id'] = $input['school_id'];
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$newTeam->students()->sync($sync_list);
					return redirect()->route('teams.index');
				} else {
					return redirect()->route('teams.create')
						->withInput($req->except([ 'students' ]))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just create the team
				Team::create($input);
				return redirect()->route('teams.index');
			}
		}

		return redirect()->route('teams.create')
			->withInput($req->except([ 'students' ]))
			->with('students', $students)
			->withErrors($teamErrors)
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
		//Breadcrumbs::addCrumb('Show Team', $id);
		View::share('title', 'Show Team');
		$team = Team::with('school')->findOrFail($id);

		return View::make('teams.show', compact('team'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		View::share('title', 'Edit Team');

		$invoices = Invoices::with([ 'school', 'user'])->orderBy('year','desc')->get();
		$team = Team::with('school')->find($id);
		$thisInvoice = Invoices::where('user_id', $team->teacher_id)->where('year', $team->year)->first();

		if (is_null($team))
		{
			return redirect()->route('teams.index');
		}

		$division_list = Division::longname_array(true);

		$invoice_list = $invoices->sort(function ($a, $b) {
			// Sort by last name
			$a_name = preg_split("/\s+/", $a->user->name);
			$b_name = preg_split("/\s+/", $b->user->name);
			return strcasecmp(array_pop($a_name), array_pop($b_name));
		})->reduce(function($acc, $invoice) {
			$acc[$invoice->year]['a' . $invoice->id]['teacher'] = $invoice->user->name . ' (' . $invoice->school->name . ')';
			$acc[$invoice->year]['a' . $invoice->id]['teacher_id'] = $invoice->user_id;
			return $acc;
		},[]);

		$yearList =  $invoices->pluck('year', 'year')->all();

		// Student Setup
		$ethnicity_list = [ 0 => "- Select Ethnicity -" ] + Ethnicity::all()->pluck('name','id')->all();
		if(!Session::has('students')) {
			// On first load we populate the form from the DB
			$students = $team->students;
		} else {
			// On subsequent loads or errors, use the sessions variable
			$students = [];
		}

		View::share('index', -1);

		return View::make('teams.edit',
			compact('team','students','yearList', 'division_list', 'ethnicity_list', 'invoice_list', 'thisInvoice'));
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
		if(!$invoice) {
			return redirect()->route('teams.edit', $id)
				->withInput()
				->withErrors(['message' => 'Teacher must be selected'])
				->with('message', 'There were validation errors.');
		}
		$input['school_id'] = $invoice->school_id;
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
					$team->update($input);
					$sync_list = [];

					foreach ($students as $index => &$student) {
						$student['year'] = $invoice->year;
						if(array_key_exists('id', $student)) {
							$newStudent = Student::find($student['id']);
							$newStudent->update($student);
						} else {
							$student['teacher_id'] = $input['teacher_id'];
							$student['school_id'] = $input['school_id'];
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$team->students()->sync($sync_list);
					return redirect()->route('teams.index');
				} else {
					return redirect()->route('teams.edit', $id)
						->withInput($req->except([ 'students' ]))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just update the team
				$team = Team::find($id);
				$team->update($input);
				return redirect()->route('teams.index');
			}
		}

		return redirect()->route('teams.edit', $id)
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
		try {
			Team::find($id)->delete();
		} catch (\Exception $e) {
			// Ignoring
		}

		Session::forget('currentCompetition');
		Session::forget('currentDivision');
		Session::forget('currentTeam');

		return redirect()->route('teams.index');
	}
}
