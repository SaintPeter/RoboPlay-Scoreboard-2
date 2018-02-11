<?php

namespace App\Http\Controllers;

use View;
use Auth;
use Session;
use Validator;
use Illuminate\Http\Request;

use App\Models\ {
    Video,
    Student,
    Invoices,
    Ethnicity,
    VideoAward,
    Vid_division
};
class VideosController extends Controller {

	/**
	 * Video Repository
	 *
	 * @var Video
	 */
	protected $video;

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		// Selected year set in filters.php -> App::before()
		$year = Session::get('year', false);

		$video_query = Video::with('vid_division',
		                           'school', 'students', 'teacher', 'awards')
							->orderBy('year', 'desc')
							->orderBy('teacher_id');

		if($year) {
			$video_query = $video_query->where('year', $year);
		}

		$videos = $video_query->get();

		View::share('title', 'Videos');
		return View::make('videos.index', compact('videos'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		View::share('title', 'Add Video');

		$invoices = Invoices::with([ 'school', 'user'])->orderBy('year','desc')->get();

		$vid_divisions = Vid_division::longname_array(true);

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
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->pluck('name','id')->all());

		// Student Setup
		$students = [];

        // Video Awards List Setup
	    $awards_list = VideoAward::all()->pluck('name', 'id')->all();

		$index = 0;
		View::share('index', $index);
		return View::make('videos.create', compact('vid_divisions', 'yearList','awards_list', 'invoice_list', 'ethnicity_list', 'students'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $req
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $req)
	{
		$input = $req->except(['students', 'awards' ]);
		$rules = Video::$rules;
		// Skip check on video for admins
		unset($rules['yt_code']);

		$invoice = Invoices::find($req->input('invoice_id'));
		if(!$invoice) {
			return redirect()->route('videos.create')
				->withInput($req->except([ 'students' ]))
				->with('students', $req->input('students'))
				->withErrors(['message' => 'Teacher must be selected'])
				->with('message', 'There were validation errors.');
		}
		$input['school_id'] = $invoice->school_id;
		$input['year'] = $invoice->year;
		$input['teacher_id'] = $invoice->user_id;

		$students = $req->input('students');

		$videoErrors = Validator::make($input, $rules);

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
							$student['teacher_id'] = $input['teacher_id'];
							$student['school_id'] = $input['school_id'];
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$newvideo->students()->sync($sync_list);
					$newvideo->awards()->sync($req->input('awards', []));
					return redirect()->route('videos.index');
				} else {
					return redirect()->route('videos.create')
						->withInput($req->except([ 'students' ]))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just create the team
				$newvideo = Video::create($input);
				$newvideo->awards()->sync($req->input('awards', []));
				return redirect()->route('videos.index');
			}
		}

		return redirect()->route('videos.create')
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
		//Breadcrumbs::addCrumb('Show Video', 'videos');
		View::share('title', 'Show Video');
		$video = Video::with('awards')->findOrFail($id);

		return View::make('videos.show', compact('video'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//Breadcrumbs::addCrumb('Edit Video', 'videos');
		View::share('title', 'Edit Video');
		$invoices = Invoices::with([ 'school', 'user'])->orderBy('year','desc')->get();
		$video = Video::with('teacher','vid_division','school','awards')->find($id);
		$thisInvoice = Invoices::where('user_id', $video->teacher_id)->where('year', $video->year)->first();


		$vid_divisions = Vid_division::longname_array(true);

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
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->pluck('name','id')->all());

        // Video Awards List Setup
	    $awards_list = VideoAward::all()->pluck('name', 'id')->all();
	    $awards_selected = $video->awards->pluck('id')->all();

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
			return redirect()->route('videos.index');
		}
		View::share('index', -1);
		return View::make('videos.edit',
			compact('video','yearList' ,'vid_divisions', 'awards_list', 'awards_selected' ,
					'invoice_list', 'ethnicity_list', 'students', 'thisInvoice'));
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
		$input = $req->except([ '_method', 'students', 'awards'  ]);
		$students = $req->input('students');

		$invoice = Invoices::find($req->input('invoice_id'));
		if(!$invoice) {
			return redirect()->route('videos.edit', $id)
				->withInput($req->except([ 'students' ]))
				->with('students', $students)
				->withErrors(['message' => 'Teacher must be selected'])
				->with('message', 'There were validation errors.');
		}

		$input['school_id'] = $invoice->school_id;
		$input['teacher_id'] = $invoice->user_id;

		$rules = Video::$rules;
		// Skip check on video code for admins
		unset($rules['yt_code']);

		$videoErrors = Validator::make($input, $rules);

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
					$video = video::find($id);
					$video->update($input);

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
					$video->students()->sync($sync_list);
					$video->awards()->sync($req->input('awards', []));
					return redirect()->route('videos.index');
				} else {
					return redirect()->route('videos.edit', $id)
						->withInput($req->except([ 'students' ]))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just update the video
				$video = video::find($id);
				$video->update($input);
				$video->awards()->sync($req->input('awards', []));
				return redirect()->route('videos.index');
			}
		}

		return redirect()->route('videos.edit', $id)
			->withInput($req->except([ 'students' ]))
			->with('students', $students)
			->withErrors($videoErrors)
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
			Video::find($id)->delete();
		} catch (\Exception $e) {
			// Ignore Exception
		}

		return redirect()->route('videos.index');
	}
}
