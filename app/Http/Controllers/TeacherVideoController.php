<?php

namespace App\Http\Controllers;

use Auth;
use View;
use Session;
use Validator;
use Illuminate\Http\Request;
use App\Enums\VideoCheckStatus;

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

	public function validate_video(Request $req, $video_id) {
		try {
			list($status, $results) = $this->check_video_files($video_id, true);
		} catch (\Exception $e) {
			return response('Cannot Find Video', 404 );
		}

		$include_javascript = true;

		return View::make('teacher.videos.partial.file_status')
			->with(compact('results', 'video_id', 'status', 'include_javascript'));
	}


	/**
	 * Check the files associated with a video and display their status
	 *
	 * @param $video_id
	 * @return (\App\Enums\VideoCheckStatus, array)
	 */
	public static function check_video_files($video_id, $write_results = false) {
		// Get the video and associated files
		$video = Video::with('files', 'files.filetype')->findOrFail($video_id);
		$files = $video->files;

		$counts = [
			'video' => 0,
			'code' => 0,
			'doc' => 0,
			'cad' => 0,
			'image' => 0,
			'script' => 0,
		];

		$code_results = [];
		$fail = false;
		$warning = false;

		// Scan files for metadata etc
		foreach($files as $file) {
			$counts[$file->filetype->type]++;

			switch($file->filetype->type) {
				case 'doc':
					// Look for scripts
					if (preg_match_all("/script/i", $file->filename)) {
						$counts['script']++;
					}
					break;
				case 'code':
					// Scan the code files
					try {
						$codeFile = file_get_contents($file->full_path());
						if (preg_match_all("/File|" .
							"Video Title|" .
							"Scene #|" .
							"Teacher Advisors|" .
							"School Name|" .
							"School District|" .
							"Code Written by|" .
							"Student Names|" .
							"Purpose/i",
							$codeFile, $matches)) {
							if (count($matches[0]) < 5) {
								$code_results[] = [
									'filename' => $file->filename,
									'message' => 'Required header missing or incomplete'
								];
							}
						}
					} catch (\Exception $e) {
						$code_results[] = [
							'filename' => $file->filename,
							'message' => "Unable to read/parse file"
						];
					}
			}
		}

		$results = [];

		// Video File Present
		if($counts['video'] > 0) {
			$results[] = [
				'status' => 'PASS',
				'message' => 'Video File Uploaded'
			];
		} else {
			$results[] = [
				'status' => 'FAIL',
				'message' => 'Video File Missing'
			];
			$fail = true;
		}

		// CAD file, but only if "Custom" selected
		if($video->has_custom) {
			if(($counts['cad'] + $counts['image']) > 0) {
				$results[] = [
					'status' => 'PASS',
					'message' => 'CAD Files Present for Custom Part'
				];
			} else {
				$results[] = [
					'status' => 'FAIL',
					'message' => 'Custom Part: CAD Files Missing',
					'note' => 'Custom Parts submissions must include CAD files or drawings.  If you ' .
						'have included them in a different file type, please contact the Video Coordinator for approval.'
				];
				$fail = true;
			}

			if($counts['doc'] > 0) {
				$results[] = [
					'status' => 'PASS',
					'message' => 'Custom Part: Explanation Files Present'
				];
			} else {
				$results[] = [
					'status' => 'FAIL',
					'message' => 'Custom Part: Explanation File(s) Missing',
					'note' => 'Custom Parts are required to have a document with at least a paragraph ' .
						      'explaining the function and use of the part.'
				];
				$fail = true;
			}
		} else {
			if(($counts['cad'] + $counts['image']) > 0) {
				$results[] = [
					'status' => 'WARNING',
					'message' => 'CAD Files Present but Custom Part flag not set'
				];
				$warning = true;
			}
		}

		// Script Present
		if($counts['script'] > 0) {
			$results[] = [
				'status' => 'PASS',
				'message' => 'Script File Present'
			];
		} else {
			$results[] = [
				'status' => 'WARNING',
				'message' => 'Script File Missing',
				'note' => 'No document file was found with string "script" in the filename. ' .
					'If your video contains no dialog and/or stage direction (such as a music video), a script is not required. ' .
					'If dialog or stage direction are present, the video will be disqualified.'
			];
			$fail = true;
		}

		// Code File(s) Present
		if($counts['code'] > 0) {
			$results[] = [
				'status' => 'PASS',
				'message' => 'Code File(s) Present'
			];
			if(count($code_results) > 0) {
				$results[] = [
					'status' => 'FAIL',
					'message' => count($code_results) . ' of ' . $counts['code'] . ' code files have issues',
					'note' => 'This is a new feature.  If you believe you have the proper headers, please ' .
						      'contact the Video Coordinator',
					'files' => $code_results
				];
				$fail = true;
			} else {
				$results[] = [
					'status' => 'PASS',
					'message' => $counts['code'] . ' of ' . $counts['code'] . ' code files have proper headers',
				];
			}
		} else {
			$results[] = [
				'status' => 'FAIL',
				'message' => 'Code File(s) Missing'
			];
			$fail = true;
		}

		if($fail) {
			$status = VideoCheckStatus::Fail;
		} elseif($warning) {
			$status = VideoCheckStatus::Warnings;
		} else {
			$status = VideoCheckStatus::Pass;
		}

		if($write_results) {
			$video->status = $status;
			$video->save();
		}

		return [$status, $results];
	}


}
