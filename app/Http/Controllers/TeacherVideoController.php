<?php

namespace App\Http\Controllers;

use Auth;
use View;
use Mail;
use Session;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Mail\VideoUpdated;
use App\Enums\VideoCheckStatus;
use App\Enums\VideoReviewStatus;

use Symfony\Component\Process\ {
	Process,
	Exception\ProcessFailedException
};

use App\Models\ {
    Files,
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

		$videoErrors = Validator::make($input, Video::$rules, Video::$customMessages);

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
		$video = Video::with('school')->findOrFail($id);

		$comp_year = CompYear::where('year', $video->year)->first();
		$edit_days = Carbon::now()->diffInDays($comp_year->edit_end, false);

		return View::make('teacher.videos.show', compact('video', 'edit_days'));
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

		$videoValidation = Validator::make($input, Video::$rules, Video::$customMessages);

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
					$input['audit'] = VideoCheckStatus::Untested;
					if($video->review_status === VideoReviewStatus::Disqualified && $video->yt_code !== $input['yt_code']) {
						$video->review_status = VideoReviewStatus::Reviewed;
						$this->send_video_updated_message($video);
					}
					$video->fill($input);
					$video->save();

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
				$input['audit'] = VideoCheckStatus::Untested;
				if($video->review_status === VideoReviewStatus::Disqualified && $video->yt_code !== $input['yt_code']) {
					$video->review_status = VideoReviewStatus::Reviewed;
					$this->send_video_updated_message($video);
				}
				$video->fill($input);
				$video->save();

				return redirect()->route('teacher.index');
			}
		}

		return redirect()->route('teacher.videos.edit', $id)
			->withInput()
			->withErrors($videoValidation)
			->with('message', 'There were validation errors.');
	}

	private function send_video_updated_message($video) {
		$coordinator = $video->division->competition->user;

		if($coordinator) {
			Mail::to($coordinator)
				->queue(new VideoUpdated($video));
		}
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
			$video = Video::with('files', 'files.filetype')->findOrFail($video_id);
			list($status, $results) = $this->check_video_files($video, true);
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
	public static function check_video_files($video, $write_results = false, $errors_only = false) {
		// Get the video and associated files
		$files = $video->files;

		$counts = [
			'video' => 0,
			'code' => 0,
			'doc' => 0,
			'cad' => 0,
			'img' => 0,
			'script' => 0,
		];

		$code_results = [];
//		$script_results = [];
//		$code_filenames = [];
		$fail = false;
		$warning = false;

		// Scan files for metadata etc
		/** @var Files $file */
		foreach($files as $file) {
			$counts[$file->filetype->type]++;

			switch($file->filetype->type) {
				case 'doc':
					// Look for scripts
					if (preg_match_all("/script/i", $file->filename)) {
						$counts['script']++;
					}

					// check for code inside files
//					try {
//						if($file->filetype->ext == "docx") {
//							if(TeacherVideoController::check_docx_for_code($file->full_path())) {
//								$script_results[] = [
//									'filename' => $file->filename,
//									'message' => 'Docx File contains Ch Code'
//								];
//							}
//						}
//
//						if($file->filetype->ext == "pdf") {
//							if(TeacherVideoController::check_pdf_for_code($file->full_path())) {
//								$script_results[] = [
//									'filename' => $file->filename,
//									'message' => 'PDF File contains Ch Code'
//								];
//							}
//						}
//					} catch (\Exception $e) {
//						$script_results[] = [
//							'filename' => $file->filename,
//							'message' => "Unable to read/parse file"
//						];
//					}

					break;
				case 'code':
					// Track filenames for order
//					$code_filenames[] = $file->filename;

					// Scan the code files
					try {
						$codeFile = file_get_contents($file->full_path());
						preg_match_all("/File|" .
							"Video Title|" .
							"Scene #|" .
							"Teacher Advisors|" .
							"School Name|" .
							"School District|" .
							"Code Written by|" .
							"Student Names|" .
							"Purpose/i",
							$codeFile, $matches);
						if (count($matches[0]) < 5) {
							$code_results[] = [
								'filename' => $file->filename,
								'message' => 'Required header missing or incomplete'
							];
						}

						preg_match_all("/<linkbot\.h>/", $codeFile, $matches);
						if(count($matches[0]) > 1) {
							$code_results[] = [
								'filename' => $file->filename,
								'message' => 'Duplicate <linkbot.h> declarations Found. Files must be stand-alone.'
							];
						}

					} catch (\Exception $e) {
						$code_results[] = [
							'filename' => $file->filename,
							'message' => "Unable to read/parse file"
						];
					}
					if(!preg_match("/\d+/", $file->filename)) {
						$code_results[] = [
							'filename' => $file->filename,
							'message' => 'Filename missing scene/sequence number'
						];
					}
			}
		}

		$results = [];

		// Check Content Tags
		$flag_count = count(array_filter([
			$video->has_story ,
			 $video->has_task ,
			 $video->has_choreo ,
			 $video->has_custom
		]));

		if($flag_count > 0 and $flag_count < 4) {
			$results[] = [
				'status' => 'PASS',
				'message' => 'Content Tags Present'
			];
		} elseif($flag_count == 4) {
			$results[] = [
				'status' => 'WARNING',
				'message' => 'All Content Tags Selected',
				'note' => 'It is the rare video which truly contains all types of content.  Are you sure that ' .
					'your video contains all of these elements? Remember that these tags help the judges ' .
					'to understand the video\'s intent.'
			];
			$warning = true;
		} else {
			$results[] = [
				'status' => 'WARNING',
				'message' => 'Content Tags Missing',
				'note' => 'While not required, videos should have at least one tag for Storyline, Choreography, ' .
					'Interesting Task, or Custom part. These tags help the judges to understand the video\'s intent.'
			];
			$warning = true;
		}

		// Video File Present
		if($counts['video'] == 1) {
			$results[] = [
				'status' => 'PASS',
				'message' => 'Video File Uploaded'
			];
		} elseif($counts['video'] > 1) {
			$results[] = [
				'status' => 'FAIL',
				'message' => "Too Many Video Files (${counts['video']} found)",
				'note' => 'You may only upload a single video file per submission. Please delete additional video files.'
			];
			$fail = true;
		} else {
			$results[] = [
				'status' => 'FAIL',
				'message' => 'Video File Missing'
			];
			$fail = true;
		}

		// CAD file, but only if "Custom" selected
		if($video->has_custom) {
			if(($counts['cad'] + $counts['img']) > 0) {
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
			if(($counts['cad'] + $counts['img']) > 0) {
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

//			if(count($script_results) > 0) {
//				$results[] = [
//					'status' => 'FAIL',
//					'message' => count($script_results) . ' of ' . $counts['script'] . ' script files have issues',
//					'note' => 'Code is not allowed in DOCX or PDF file formats.',
//					'files' => $script_results
//				];
//				$fail = true;
//			}
		} else {
			$results[] = [
				'status' => 'WARNING',
				'message' => 'Script File Missing',
				'note' => 'No document file was found with string "script" in the filename. ' .
					'All Videos must include a script, including choreography videos, much must contain stage direction.'
			];
			$warning = true;
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
					'note' => 'Be sure you are using the header template found in the Call for Participation. ' .
						'Files may only have a single "file" worth of code in them.',
					'files' => $code_results
				];
				$fail = true;
			} else {
				$results[] = [
					'status' => 'PASS',
					'message' => $counts['code'] . ' of ' . $counts['code'] . ' code files are formatted properly',
				];
			}
		} else {
			$results[] = [
				'status' => 'FAIL',
				'message' => 'Code File(s) Missing'
			];
			$fail = true;
		}

		// Video File Order Check
//		if(count($code_filenames) > 0) {
//			sort($code_filenames);
//			$current_number = -99999;
//			foreach($code_filenames as $filename) {
//				$num = intval(preg_replace('/\D/','',$filename));
//				if($num < $current_number) {
//					$results[] = [
//						'status' => 'WARNING',
//						'message' => 'Code files may display out of order',
//						'note' => 'Files should have a common text prefix and pad numbers with leading zeroes to ' .
//							' ensure they sort properly. ' .
//							'IE: Little_Red_Riding_Bot_scene_01.ch, Little_Red_Riding_Bot_scene_02.ch, etc.'
//					];
//					$warning = true;
//					break;
//				} else {
//					$current_number = $num;
//				}
//			}
//		}

		if($fail) {
			$status = VideoCheckStatus::Fail;
		} elseif($warning) {
			$status = VideoCheckStatus::Warnings;
		} else {
			$status = VideoCheckStatus::Pass;
		}

		if($errors_only) {
			$results = array_filter($results, function($result) {
				return $result['status'] != "PASS";
			});
		}

		if($write_results) {
			$video->status = $status;
			$video->save();
		}

		return [$status, $results];
	}

	public static function check_docx_for_code($filename) {
		$process = new Process([ config("services.docx2txt.exe"), $filename, '-']);
		$process->setTimeout(10);
		$process->run();

		if (!$process->isSuccessful()) {
			$exception = new ProcessFailedException($process);
			throw $exception;
		}

		$output = $process->getOutput();
		if(preg_match('/<linkbot\.h>/',$output)) {
			return true;
		}

		return false;
	}

	public static function check_pdf_for_code($filename) {
		$process = new Process([ config("services.pdftotext.exe"), $filename, '-']);
		$process->setTimeout(10);
		$process->run();

		if (!$process->isSuccessful()) {
			$exception = new ProcessFailedException($process);
			throw $exception;
		}

		$output = $process->getOutput();
		if(preg_match('/<linkbot\.h>/',$output)) {
			return true;
		}
		return false;
	}
}
