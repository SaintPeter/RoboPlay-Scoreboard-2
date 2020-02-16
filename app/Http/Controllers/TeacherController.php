<?php

namespace App\Http\Controllers;

use App\Enums\VideoCheckStatus;
use Auth;
use View;
use Illuminate\ {
	Http\Request,
	Routing\Controller
};

use App\Models\ {
    Student,
    Invoices,
    CompYear,
    Ethnicity,
    Math_Level
};

use Carbon\Carbon;

class TeacherController extends Controller {

	/**
	 * Display a listing of the resource.
	 * GET /teacher
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function index()
	{
		$comp_year = CompYear::current();
	    $year = $comp_year->year;
	    $invoice = Invoices::where('year', $year)
	                       ->where('user_id', Auth::user()->id)
	                       ->with( [ 'videos' => function($q) use ($year) {
	                            return $q->where('year', $year);
	                       }, 'videos.students'])
	                       ->with( [ 'teams' => function($q) use ($year) {
	                            return $q->where('year', $year);
	                       }, 'teams.students', 'teams.division', 'teams.division.competition'])
                           ->with('user', 'school')
	                       ->first();

		if(!isset($invoice)) {
			return View::make('error', [ 'title' => 'Missing Registration',
			                             'error_title' => 'No Registration Found',
			                             'message' => 'We do not have a record of a registration for your school. <br>' .
                                                      "You can find the registration link <a href='http://c-stem.ucdavis.edu/c-stem-day/overview/$year-c-stem-day/'>here</a>.<br><br>" .
                                                      'If you have just registered, it can take up to 3 hours for a scoreboard sync to occur.<br><br>' .
                                                      "For Registration Support, you can e-mail us at <a href='mailto:roboplay@c-stem.ucdavis.edu?subject=C-STEM Day $year Registration Issue'>roboplay@c-stem.ucdavis.edu</a>"]);
		}

		$school_id = $invoice->school_id;

		if($school_id == 0 || is_null($invoice->school)) {
			return View::make('error', [ 'title' => 'User Data Error',
			                             'error_title' => 'User&apos;s School Id is not set properly',
			                             'message' => 'Your user record&apos;s school id is either not set or not set properly.  We cannot load your school registration.<br><br>' .
			                             "E-mail us this error message and your wordpress username at <a href='mailto:roboplay@c-stem.ucdavis.edu?subject=C-STEM Day $year - User Data Error'>roboplay@c-stem.ucdavis.edu</a> for support."]);
		}

		$tshirt_sizes = [
			0 => '- Pick T-shirt Size -',
	        'XS' => 'XS - Extra Small',
	        'S' => 'S - Small',
	        'M' => 'M - Medium',
	        'L' => 'L - Large',
	        'XL' => 'XL - Extra Large',
	        'XXL' => 'XXL - Extra, Extra Large',
			'3XL' => '3XL - Triple Extra Large'
		];

		switch($invoice->paid) {
			case 0:
				$paid = 'Unpaid'; break;
			case 1:
				$paid = 'Paid'; break;
			case 2:
				$paid = 'Pending'; break;
			case 3:
				$paid = 'Canceled'; break;
		}

		$school = $invoice->school;
		$teams = $invoice->teams;
		$videos = $invoice->videos;

		// Check to make sure that all teams are at the same competition
		$competition_error = false;
		if(count($teams)) {
			$first_team = $teams->first()->division->competition_id;
			foreach($teams as $team) {
				if($first_team != $team->division->competition_id) {
					$competition_error = true;
				}
			};
		}

		// Make sure there are no failing videos
		$validation_error = false;
		if(count($videos)) {
			foreach($videos as $video) {
				if($video->status != VideoCheckStatus::Pass) {
					$validation_error = true;
				}
			}
		}

		$reg_days = Carbon::now()->diffInDays($comp_year->reminder_end,false);
		$edit_days = Carbon::now()->diffInDays($comp_year->edit_end, false);

		View::share('title', 'Manage Teams');
        return View::make('teacher.index',
	        compact('invoice', 'teams', 'videos',
	            'math_teams', 'school', 'paid', 'tshirt_sizes',
		        'reg_days', 'edit_days', 'comp_year',
		        'competition_error', 'validation_error')
        );

	}

	// Saves current user t-shirt size
	public function save_tshirt(Request $req) {
	    $user = Auth::user();
	    $user->update([ 'tshirt' => $req->input('tshirt', '') ]);

	    return 'true';
	}

	// Returns a view with a new blank student form
	public function ajax_blank_student($index) {
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->pluck('name','id')->all());
		return View::make('students.partial.create_empty')->with(compact('index', 'ethnicity_list'));
	}

	/**
	 * Returns a view with a table of unattached students for a given type
	 * @param Request $req
	 * @param $type
	 * @param null $teacher_id
	 * @return $this
	 */
	public function ajax_student_list(Request $req, $type, $teacher_id = null) {
		$current_students = $req->input('current_students', []);

		// If the teacher_id is not set, use the current user's id
		if(!$teacher_id) {
			$teacher_id = Auth::user()->id;
		}

		//  Get one or more schools where the teacher teacher
		$school_ids = Invoices::where('user_id', $teacher_id)->pluck('school_id', 'school_id')->all();

		// Find all students from that school OR from that teacher
		$student_query = Student::with('teacher')
						->whereIn('school_id', $school_ids)
						->orWhere('teacher_id', $teacher_id);

		// Select students where they are not attached to the given type
		switch($type) {
			case 'teams':
				$student_query = $student_query->has('teams', '=', 0);
				break;
			case 'videos':
				$student_query = $student_query->has('videos', '=', 0);
				break;
			case 'maths':
				$student_query = $student_query->has('maths', '=', 0);
				break;
		}

		// Ignore students who are already on the current form
		if(count($current_students) > 0) {
			$student_query = $student_query->whereNotIn('id', $current_students);
		}

		// Run query
		$student_list = $student_query->get();

		// Group by teacher names
		$students = [];
		foreach($student_list as $student) {
			$students[$student->teacher->name][$student->id] = [
				'name' => $student->fullName(),
				'year' => $student->year
				];
		}

		// Sort by last name
		foreach($students as $teacher => $my_students) {
			uasort($students[$teacher], function ($a, $b) {
				// Sort by last name
				$a_name = preg_split("/\s+/", $a['name']);
				$b_name = preg_split("/\s+/", $b['name']);
				return strcasecmp(array_pop($a_name), array_pop($b_name));
			});
		}

		return View::make('students.partial.list')->with(compact('students'));
	}

	/**
	 * Return the forms for editable students based on a POSTed list
	 * @param Request $req
	 * @param $index
	 * @return $this
	 */
	public function ajax_load_students(Request $req, $index) {
		$student_list = $req->input('students');
		$students = Student::whereIn('id', $student_list)->get();

		// Ethnicity List Setup
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->pluck('name','id')->all());

		return View::make('students.partial.edit_list')->with(compact('students', 'ethnicity_list', 'index'));
	}

	/**
	 * Do the import from a CSV file into the form
	 * @param Request $req
	 * @return $this|string
	 */
	public function ajax_import_students_csv(Request $req) {
	    // Validate that a file was sent
	    if(!$req->hasFile('csv_file')) {
	        return 'nofile';
	    }

		$field_names = [
			"First Name" => 'first_name',
			"Middle/Nick Name" => 'middle_name',
			"Last Name" => 'last_name',
			"Gender" => 'gender',
			"Ethnicity" => 'ethnicity_id',
			"Grade" => 'grade',
			"E-mail" => 'email',
			"T-Shirt" => 'tshirt',
			"Math Level" => 'math_level_id' ];

		$ethnicity_decode = Ethnicity::all()->pluck('id', 'name')->all();
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->pluck('name','id')->all());

		$math_decode = Math_Level::all()->pluck('id', 'name')->all();

		$csv = new \parseCSV($req->file('csv_file')->getRealPath());
		$rawData = $csv->data;

		// Ensure we even have any data
		if(count($rawData) < 1) {
		    return 'nodata';
		}

		// Index doesn't matter because things get renumbered in the view
		$index = 0;

		// Empty container for error handling
		$students = [];

		// Clean up/translate data, fix field names
		foreach($rawData as $csv_line) {
			if(!empty($csv_line['First Name'])) {
				foreach($field_names as $import_field => $proper_field) {
				    // Set a default value
					$students[$index][$proper_field] = "";

					if(array_key_exists($import_field, $csv_line)) {
					    // Ethnicity Field Decode
						if($import_field == 'Ethnicity') {
							if(array_key_exists($csv_line[$import_field], $ethnicity_decode)) {
								$students[$index][$proper_field] = $ethnicity_decode[$csv_line[$import_field]];
							}
						// Math Level Decode
						} elseif ($proper_field == 'math_level_id') {
                            if(array_key_exists($csv_line[$import_field], $math_decode)) {
								$students[$index][$proper_field] = $math_decode[$csv_line[$import_field]];
							}
						} else {
							$students[$index][$proper_field]  = $csv_line[$import_field];
						}
					}
				}
				$students[$index]['nickname'] = 0;
				$index++;
			}
		}

		$index = $req->input('index');

        if(count($students)) {
            return View::make('students.partial.edit_list')->with(compact('students', 'ethnicity_list', 'index'));
        } else {
            return 'nodata';
        }
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /teacher/create
	 *
	 * @return void
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /teacher
	 *
	 * @param Request $req
	 * @return void
	 */
	public function store(Request $req)
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /teacher/{id}
	 *
	 * @param  int $id
	 * @return void
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /teacher/{id}/edit
	 *
	 * @param  int $id
	 * @return void
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /teacher/{id}
	 *
	 * @param Request $req
	 * @param  int $id
	 * @return void
	 */
	public function update(Request $req, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /teacher/{id}
	 *
	 * @param  int $id
	 * @return void
	 */
	public function destroy($id)
	{
		//
	}

}