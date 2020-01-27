<?php

namespace App\Http\Controllers;

use View;
use Session;
use Password;
use Carbon\Carbon;
use App\Notifications\UserCreated;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Response;

use Illuminate\Database\Eloquent\Relations\HasMany;

use App\{
	Enums\UserTypes, Models\Schools, Models\Score_run, Models\Team, Models\User, Models\Video, Models\School, Models\Frm_items, Models\CompYear, Models\Invoices, Models\Wp_user, Models\Wp_invoice_table
};
class InvoiceReview extends Controller {

	function invoicer($year = 0) {
		$year = CompYear::yearOrMostRecent($year);
		$yearList = json_encode(CompYear::orderBy('year')->pluck('year'));

		return View::make('invoicer.index')
			->with(compact('year','yearList'));
	}

	/**
	 * Display a listing of the resource.
	 * GET /invoicereview
	 *
	 * @param int $year
	 * @param int $terse
	 * @return \Illuminate\Http\Response
	 */
	public function invoice_review($year = 0, $terse = 0)
	{
		View::share('title', 'Invoice Review');

	    $year = CompYear::yearOrMostRecent($year);
	    $comp_years = CompYear::orderBy('year')->get();

		$invoices = Invoices::with( 'user', 'school')
	                        ->with( [ 'videos' => function(HasMany $q) use ($year) {
	                             return $q->where('year', $year);
	                        }, 'videos.students'])
	                        ->with( [ 'teams' => function(HasMany $q) use ($year) {
	                             return $q->where('year', $year);
	                        }, 'teams.students', 'teams.students.math_level', 'teams.division'])
    	                    ->where('year', $year)
    	                    ->get();

		$last_sync_date = $invoices->max('updated_at');
		if(isset($last_sync_date)) {
		    $last_sync = $last_sync_date->format('D, F j, g:s a');
		} else {
		    $last_sync = "Never";
		}

        // Callback for reduce to get a total student count
        $student_count = function($curr, $next) {
            return $curr + $next->students->count();
        };

        $comp_year = CompYear::where('year', $year)
                                     ->with('vid_divisions', 'divisions', 'divisions.competition')
                                     ->first();

        $vid_division_list = $comp_year->vid_divisions->pluck('name', 'id')->all();

        foreach($comp_year->divisions as $division) {
            $division_list[$division->competition->name][$division->id] = $division->longname();
        }
        //$division_list = $comp_year->divisions->pluck('name','id')->all();

		if($terse) {
			return View::make('invoice_review.usernames',
						compact('invoices', 'year',
		                        'student_count', 'last_sync',
		                        'vid_division_list', 'division_list',
		                        'comp_years'));
		}

		return View::make('invoice_review.index',
		                compact('invoices', 'year',
		                        'student_count', 'last_sync',
		                        'vid_division_list', 'division_list',
		                        'comp_years'));
	}

	public function toggle_video($video_id) {
	    $video = Video::findOrFail($video_id);
	    $video->update(['audit' => !$video->audit ]);
	    return 'true';
	}

	public function toggle_team($team_id) {
	    $team = Team::findOrFail($team_id);
	    $team->update(['audit' => !$team->audit ]);
	    return 'true';
	}

	// Toggles the status of 'paid' for the given invoice
	public function toggle_paid($invoice_id) {
	    $invoice = Invoices::findOrFail($invoice_id);
	    $invoice->update(['paid' => !$invoice->paid ]);
	    return 'true';
	}

	// Set paid and notes
	public function set_paid(Request $req,$invoice_id) {
	    $invoice = Invoices::findOrFail($invoice_id);
	    $invoice->update(['paid' => 1, 'notes' => $req->input('notes', '') ]);
	    return 'true';
	}

	// Clear paid and notes
	public function clear_paid($invoice_id) {
	    $invoice = Invoices::findOrFail($invoice_id);
	    $invoice->update(['paid' => 0, 'notes' => '' ]);
	    return 'true';
	}

	public function save_video_notes(Request $req,$video_id) {
	    $video = Video::findOrFail($video_id);
	    $video->update(['notes' => $req->input('notes', '') ]);
	    return 'true';
	}

	public function save_video_division($video_id, $vid_div_id) {
	       $video = Video::findOrFail($video_id);
	    $video->update(['vid_division_id' => $vid_div_id ]);
	    return 'true';
	}

	public function save_team_division($team_id, $div_id) {
	    $team = Team::findOrFail($team_id);
	    $team->update(['division_id' => $div_id ]);
	    return 'true';
	}


	public function invoice_sync($year = 0, $online = true, $sync_db = false)
	{
		// If we are to sync the db, do it first
		if($sync_db) {
			$result = Artisan::call('scoreboard:sync_db');
		}

		$message = "Invoice Type Not Found";
	    $comp_year = CompYear::where('year', $year)->firstOrFail();
	    $userList = [];

	    // C-STEM Invoices (2014-2016)
	    if($comp_year->invoice_type == 1) {

    	    $raw_invoices = Wp_invoice_table::with('invoice_data', 'wp_user', 'wp_user.usermeta')
    									->where('invoice_type_id', $comp_year->invoice_type_id)->get();

            $count = 0;
            foreach($raw_invoices as $raw_invoice) {
                // Fetch a local invoice if it exists
                $invoice = Invoices::firstOrNew([
                    'remote_id' => $raw_invoice->invoice_no,
                    'user_id' => $raw_invoice->user->ID,
                    'year' => $year
                ]);

                $invoice->school_id = intval($raw_invoice->wp_user->getMeta('wp_school_id',0));

                $invoice->team_count = $raw_invoice->getData('Challenge', 0) + $raw_invoice->getData('Challenge2', 0);
                $invoice->video_count = $raw_invoice->getData('Video', 0);
                $invoice->math_count = $raw_invoice->getData('PreMath', 0) + $raw_invoice->getData('AlgMath', 0);

                $invoice->paid = $raw_invoice->paid;

	            // Store the users
	            $userList[$invoice->user_id] = $invoice->user_id;

                $invoice->save();
                $count++;
            }

            // Check for removed invoices
            $invoices = Invoices::where('year', $year)->get();
            $raw_invoice_array = $raw_invoices->pluck('invoice_no')->all();
            $removed = 0;
            foreach($invoices as $invoice) {
                if(!in_array($invoice->remote_id, $raw_invoice_array)) {
                    $invoice->delete();
                    $removed++;
                }
            }

            $this->school_sync();

            $message = 'Synced ' . $count . " Invoices, Removed $removed for $year";

            // Only redirect if online
            if($online) {
                return redirect()->route('invoice_review', $year)->with('message', $message);
            } else {
                return $message;
            }

        }
        // Formidable Forms, 2017-??
        if($comp_year->invoice_type == 2) {
            // Get list of Invoices
            $raw_invoices = Frm_items::with('fields', 'values')
                                     ->where('form_id', $comp_year->invoice_type_id)
                                     ->get();

            /*
            __ 2017 Field Information __
            field_id   Description
            966        Video Competition ($20 per team)
            965        Challenge Competition - Complete Package ($320 per team)
            964        Challenge Competition - Basic Package ($250 per team)
            961        wp_school_id from usermeta

            __ 2018 Field Information__
            field_id   Description
            1703       Video Competition
			1702       Challenge Competition - Complete Package ($320 per team)
            N/A        Challenge Competition - Basic Package - Not included this year
			1711       wp_school_id entry from usermeta

            __ 2019 Field Information__
            field_id   Description
            2320       Video Competition
			2318       Challenge Competition - Complete Package ($320 per team)
            N/A        Challenge Competition - Basic Package - Not included this year
			2329       wp_school_id entry from usermeta

            __ 2020 Field Information__
            field_id   Description
            3892       Video Competition
			3889       Challenge Competition - Complete Package ($320 per team)
            N/A        Challenge Competition - Basic Package - Not included this year
			3901       wp_school_id entry from usermeta
            */

	        $field_ids = [
		        '2017' => [
			        'video' => 966,
			        'challenge_complete' => 965,
			        'challenge_basic' => 964,
			        'school_id' => 961
		        ],
		        '2018' => [
					'video' => 1703,
					'challenge_complete' => 1702,
					'school_id' => 1711
		        ],
		        '2019' => [
			        'video' => 2320,
			        'challenge_complete' => 2318,
			        'school_id' => 2329
		        ],
		        '2020' => [
			        'video' => 3892,
			        'challenge_complete' => 3889,
			        'school_id' => 3901
		        ]
	        ];

            $field_lookup = $field_ids[$year];

            $count = 0;
            foreach($raw_invoices as $raw_invoice) {
                // Create a list of values to lookup from
                $vals = $raw_invoice->values->pluck('meta_value', 'field_id')->all();

                // Fetch a local invoice if it exists
                $invoice = Invoices::firstOrNew([
                    'remote_id' => $raw_invoice->id,
                    'user_id' => $raw_invoice->user_id,
                    'year' => $year
                ]);

                $invoice->school_id = intval($this->arr_get($vals[$field_lookup['school_id']],0)); // School id

                // Complete Packages
                $invoice->team_count = intval($this->arr_get($vals[$field_lookup['challenge_complete']],0));

                // Basic Packagae, if it exists
	            if(array_key_exists('challenge_basic', $field_lookup)) {
	            	$invoice->team_count += intval($this->arr_get($vals[$field_lookup['challenge_basic']],0));
	            }

                // Video Package
                $invoice->video_count = intval($this->arr_get($vals[$field_lookup['video']],0));

                // Not doing this anymore
                $invoice->math_count = 0;

                // Store the users
                $userList[$invoice->user_id] = $invoice->user_id;

                $invoice->save();
                $count++;
            }

            // Check for removed invoices
            $invoices = Invoices::where('year', $year)->get();
            $raw_invoice_array = $raw_invoices->pluck('id')->all();
            $removed = 0;
            foreach($invoices as $invoice) {
                if(!in_array($invoice->remote_id, $raw_invoice_array)) {
                    $invoice->delete();
                    $removed++;
                }
            }

            $this->school_sync();

            $message = 'Synced ' . $count . " Invoices, Removed $removed for $year";
        }

        // Create Local Users
        if(count($userList)) {
	    	$message .= '<br>' . $this->create_invoice_users($userList);
	    }

		// Only redirect if online
		if($online) {
			return redirect()->route('invoice_review', $year)->with('message', $message);
		} else {
			return str_replace("<br>", "\n", $message);
		}
	}

	public static function create_invoice_users($userList, $skip_notification = false) {
		$users = User::whereIn('id',$userList)->with('password_resets')->get();

		// Check to see if the users have already been created
		$notifyUsers = [];
		foreach($users as $user) {
			if($userList[$user->id]) {
				// If a user exists but has not had a password reset
				// we still need to notify them
				if(!$user->password_resets) {
					$notifyUsers[$user->id] = $user->id;
				}
				unset($userList[$user->id]);
			}
		}

		// Any users left will need to be created
		if(count($userList) || count($notifyUsers)) {
			$wpUserData = Wp_user::whereIn('ID', $userList)->with('usermeta')->get();

			$newUserData = [];
			foreach($wpUserData as $wpUser) {
				$newUserData[] = [
					'id' => $wpUser->ID,
					'name' => $wpUser->getName(),
					'email' => $wpUser->user_email,
					'roles' => UserTypes::Teacher,
					'password' => '',
					'tshirt' => '',
					'remember_token' => '',
					'created_at' => Carbon::now(),
					'updated_at' => Carbon::now()
				];
			}

			User::insert($newUserData);

			if(!$skip_notification) {
				$newUserList = array_merge(array_pluck($newUserData, 'id'),$notifyUsers);
				$newUsers = User::whereIn('id', $newUserList)->get();

				foreach ($newUsers as $newUser) {
					// Send Password reset notifications
					$token = Password::getRepository()->create($newUser);
					$newUser->notify(new UserCreated($token));
				}
				return count($userList) . " New Users Created, " . count($notifyUsers) . " Users Notified.";
			}
			return count($userList) . " New Users Created, " . count($notifyUsers) . " Unique Users Scanned.";
		}
		return 'No new users created';
	}

    // Make a flat, local copy of the wordpress schools table
	public function school_sync() {
	    $invoices = Invoices::all();
	    $school_list = $invoices->pluck('school_id')->all();
	    $wp_schools = Schools::whereIn('school_id', $school_list)->with('district', 'district.county')->get();
	    $schools = School::all()->keyBy('id');

	    foreach($wp_schools as $this_school) {
	        if(!$schools->has($this_school->school_id)) {
	            $new_school = School::firstOrNew([
	                'id' => $this_school->school_id ]);
	            $new_school->fill([
	                'county_id' => $this_school->district->county->county_id,
	                'district_id' => $this_school->district->district_id,
	                'name' => $this_school->name,
	                'district' => $this_school->district->name,
	                'county' => $this_school->district->county->name
	                ]);
	           $new_school->save();
	           $schools->add($new_school);
	        }
	    }
	}

	// Data Export interface
	public function data_export($year = '')
	{
	    //Breadcrumbs::addCrumb('Data Export');
		View::share('title', 'Data Export');

		// Load a year from session if it's not set in the URL
		if(!$year) {
			$year = Session::get('year', $year);
		}

	    return View::make('data_export.index', compact('year'));
	}

	public function student_tshirts_csv($year = '')
	{
	    if(!$year) {
	        return redirect()->route('data_export')->with('message', 'Year must be set to export data');
	    }


		$invoices = Invoices::with('user', 'school')
	                        ->with( [ 'teams' => function($q) use ($year) {
	                             return $q->where('year', $year);
	                        }, 'teams.students', 'teams.division', 'teams.division.competition'])
    	                    ->where('year', $year)
    	                    ->get();

        // Header
	    $content = "Teacher Name, Site, Team Name, Student Name, Size\n";

		foreach($invoices as $invoice) {
		    foreach($invoice->teams as $team) {
		        foreach($team->students as $student) {
        			$content .= '"';
        			$content .= join('","',
        			                [ $invoice->user->name,
	                                $team->division->competition->location,
	                                $team->name,
	                                $student->fullName(),
	                                ($student->tshirt) ? $student->tshirt : 'Not Selected'
								   ]) .
						     '"' . "\n";
				}
			}
		}

		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="student_tshirts_' . $year . '.csv"'
		));
	}

	public function teacher_tshirts_csv($year = '')
	{
	    if(!$year) {
	        return redirect()->route('data_export')->with('message', 'Year must be set to export data');
	    }


		$invoices = Invoices::with('user', 'school')
	                        ->with( [ 'teams' => function($q) use ($year) {
	                             return $q->where('year', $year);
	                        }, 'teams.division', 'teams.division.competition'])
    	                    ->where('year', $year)
    	                    ->where('team_count', '>', 0)
    	                    ->get();

        // Header
	    $content = "Teacher Name, Site, Size\n";

		foreach($invoices as $invoice) {
		    $team = $invoice->teams->first();
		    if($team) {
    			$content .= '"';
    			$content .= join('","',[
    				            $invoice->user->name,
                                $team->division->competition->location,
                                ($invoice->user->tshirt) ? $invoice->user->tshirt : 'Not Selected',
    						   ]) .
    				     '"' . "\n";
    	    }
		}

		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="teacher_tshirts_' . $year . '.csv"'
		));

	}

	public function teacher_teams_csv($year = '')
	{
	    if(!$year) {
	        return redirect()->route('data_export')->with('message', 'Year must be set to export data');
	    }


		$invoices = Invoices::with( 'user', 'school')
	                        ->with( [ 'teams' => function($q) use ($year) {
	                             return $q->where('year', $year);
	                        }, 'teams.students', 'teams.division', 'teams.division.competition'])
    	                    ->where('year', $year)
    	                    ->get();

        // Header
	    $content = "Teacher Name,School Name,Team Name,Site,Division,Level\n";

		foreach($invoices as $invoice) {
		    foreach($invoice->teams as $team) {
    			$content .= '"';
    			$content .= join('","',
    			                [
    			                $invoice->user->name,
    			                ($invoice->school) ? $invoice->school->name : "(Not Set)",
                                $team->name,
                                $team->division->competition->location,
                                $team->division->name,
                                $team->division->level
							   ]) .
					     '"' . "\n";
			}
		}

		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="teacher_teams_' . $year . '.csv"'
		));
	}

	public function student_demographics_csv($year = '')
	{
	    if(!$year) {
	        return redirect()->route('data_export')->with('message', 'Year must be set to export data');
	    }


		$invoices = Invoices::with([ 'school', 'user' ,
		                             'teams' => function($q) use ($year) {
	                                       return $q->where('year', $year);
	                                },
	                                'teams.students', 'teams.division', 'teams.division.competition',
	                                'teams.students.math_level', 'teams.students.ethnicity'])
    	                    ->where('year', $year)
    	                    ->get();

        // Header
	    $content = "Teacher Name,School,District,County,Site,Team Name,Student Name,Gender,Ethnicity,Grade,Math Level,Math Div,Division\n";

		foreach($invoices as $invoice) {
		    foreach($invoice->teams as $team) {
		        foreach($team->students as $student) {
        			$content .= '"';
        			$content .= join('","',
        			                [ $invoice->user->name,
        			                isset($invoice->school) ? $invoice->school->name : "No School",
        			                isset($invoice->school) ? $invoice->school->district : "No School",
        			                isset($invoice->school) ? $invoice->school->county : "No School",
	                                $team->division->competition->location,
	                                $team->name,
	                                preg_replace('/"/','""',$student->fullName()),
	                                $student->gender,
	                                $student->ethnicity->name,
	                                $student->grade,
	                                $student->math_level->name,
	                                $student->math_level->level,
	                                $team->division->name
								   ]) .
						     '"' . "\n";
				}
			}
		}

		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="student_demographics_' . $year . '.csv"'
		));
	}

	public function video_demographics_csv($year = '')
	{
	    if(!$year) {
	        return redirect()->route('data_export')->with('message', 'Year must be set to export data');
	    }


		$invoices = Invoices::with('user', 'school')
	                        ->with( [ 'videos' => function($q) use ($year) {
	                             return $q->where('year', $year);
	                        }, 'videos.students', 'videos.division', 'videos.division.competition',
	                           'videos.students.math_level', 'videos.students.ethnicity'])
    	                    ->where('year', $year)
    	                    ->get();

        // Header
	    $content = "Teacher Name,School,District,County,Video Name,Status,Student Name,Gender,Ethnicity,Grade,Math Level,Math Div,Division\n";

		foreach($invoices as $invoice) {
		    foreach($invoice->videos as $video) {
		        foreach($video->students as $student) {
        			$content .= '"';
        			$content .= join('","',
        			                [ $invoice->user->name,
        			                isset($invoice->school) ? $invoice->school->name : "No School",
        			                isset($invoice->school) ? $invoice->school->district : "No School",
        			                isset($invoice->school) ? $invoice->school->county : "No School",
	                                $video->name,
					$video->flag == 0 ? 'Normal' : 'Disqualified',
	                                preg_replace('/"/','""',$student->fullName()),
	                                $student->gender,
	                                $student->ethnicity->name,
	                                $student->grade,
	                                $student->math_level->name,
	                                $student->math_level->level,
	                                $video->division->name
								   ]) .
						     '"' . "\n";
				}
			}
		}

		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="video_demographics_' . $year . '.csv"'
		));
	}

	public function challenge_runs_csv($year = '')
	{
		if(!$year) {
			return redirect()->route('data_export')->with('message', 'Year must be set to export data');
		}

		$divisions = CompYear::where('year',$year)->first()->divisions->pluck('id')->all();

		$scores = Score_run::whereIn('division_id', $divisions)
			->with('division', 'division.competition','team','challenge', 'challenge.divisions')
			->orderBy('challenge_id','run_number')
			->whereNull('deleted_at')
			->get();

		$scoreSummary = [];
		foreach($scores as $run) {
			$order = $run->challenge->divisions->first()->pivot->display_order;
			$challenge_name = sprintf("%02d. %s", $order, $run->challenge->display_name);
			$scoreSummary[$run->division->name][$run->team->name][$challenge_name][] = [
				'scores' => $run->scores,
				'abort' => $run->abort,
				'total' => $run->total,
				'location' => $run->division->competition->location
				];
		}

		// Header
		$content = "Location,Division,Team,Challenge,Run,Score,Abort,s1,s2,s3,s4,s5,s6\n";

		foreach($scoreSummary as $division => $teams) {
			foreach($teams as $team => $challenges) {
				foreach($challenges as $challenge => $runs) {
					foreach($runs as $run_number => $run) {
						$content .= '"' . join('","', [
							$run['location'],
							$division,
							$team,
							$challenge,
							$run_number + 1,
							$run['total'],
							$run['abort'] ? 'Abort' : 'Normal'
						]) . '",';
						$content .= join(",", $run['scores']) . "\n";
					}
				}
			}
		}

		//dd($content);

		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="challenge_runs_' . $year . '.csv"'
		));
	}

	public function challenge_judge_detail_csv($year = '')
	{
		if(!$year) {
			return redirect()->route('data_export')->with('message', 'Year must be set to export data');
		}

		$divisions = CompYear::where('year',$year)->first()->divisions->pluck('id')->all();

		$scores = Score_run::whereIn('division_id', $divisions)
			->with('judge')
			->orderBy('judge_id')
			->whereNull('deleted_at')
			->get();

		$scores_by_judge = $scores->groupBy('judge_id');

		$judges = $scores->pluck('judge', 'judge_id');

		// Header
		$content = "Judge,Judge Email,Year,Scored Runs\n";

		foreach($scores_by_judge as $judge_id => $scores) {
			$judge = $judges->get($judge_id);
			$content .= join(",",[
				$judge->name,
				$judge->email,
				$year,
				count($scores)
			]) . "\n";
		}

		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="challenge_judge_detail_' . $year . '.csv"'
		));
	}

	// Gets an array element or returns a default value
	function arr_get(&$var, $default=null) {
		return isset($var) ? $var : $default;
	}

}
