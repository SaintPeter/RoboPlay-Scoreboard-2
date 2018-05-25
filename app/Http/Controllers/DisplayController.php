<?php

namespace App\Http\Controllers;

use DB;
use View;
use Auth;
use Cache;
use Session;
use Response;
use App\Helpers\Roles;
use App\Enums\VideoFlag;
use Illuminate\Http\Request;

use App\ {
    Models\Video,
    Models\Vid_competition,
    Models\Competition,
    Models\Score_run,
    Models\CompYear,
    Models\Team,
    Models\Challenge,
    Models\Schedule,
    Models\Division
};

use \Carbon\Carbon;

class DisplayController extends Controller {

    // Get the current and next events and the current time
    public function init_timer() {
        $timer = new \stdClass;
		$timer->start_time = Carbon::now()->setTimezone('America/Los_Angeles')->toTimeString();
		$timer->this_event = Schedule::where('start', '<', $timer->start_time)->orderBy('start', 'DESC')->first();
		$timer->next_event = Schedule::where('start', '>', $timer->start_time)->orderBy('start')->first();
		if(isset($timer->start_time) AND isset($timer->this_event) AND isset($timer->next_event)) {
		    return $timer;
		} else {
		    return null;
		}
    }

	/**
	 * Show the detailed score for a single team for all challenges
	 *
	 * @param $team_id
	 * @param null $show_users
	 * @return \Illuminate\Http\Response
	 */
	public function teamscore($team_id, $show_users = null)
	{
		$team = Team::with('division', 'division.competition')->find($team_id);
		$division_id = $team->division->id;

		$div_challenges = Division::with([ 'challenges' => function($q) use ($division_id) {
			return $q->where('division_id', $division_id);
		}])->find($division_id)->challenges;

		if(Roles::isJudge()) {
			$scores = Score_run::with('user')->where(['team_id' => $team_id, 'division_id' => $division_id])->withTrashed()->get();
		} else {
			$scores = Score_run::with('user')->where(['team_id' => $team_id, 'division_id' => $division_id])->get();
		}

		$grand_total = 0;
		foreach($div_challenges as $div_challenge)
		{
			$challenge_number = $div_challenge->pivot->display_order;
			$challenge_list[$challenge_number]['name'] = $div_challenge->display_name;
			$challenge_list[$challenge_number]['points'] = $div_challenge->points;

			$score_runs = $scores->filter(function($score) use ($div_challenge) {
				return $score->challenge_id == $div_challenge->id;
			});

			if(isset($score_runs) && $score_runs->count() > 0)
			{
				$challenge_list[$challenge_number]['score_count'] = $score_runs->count();
				$challenge_list[$challenge_number]['score_max'] = $score_runs->filter(function($sr) { return !$sr->trashed(); } )->max('total');
				$grand_total += $challenge_list[$challenge_number]['score_max'];

				foreach($score_runs as $score_run)
				{

					$thisRun = [
						'run_time' => $score_run->run_time,
						'total'    => $score_run->total,
						'user'     => $score_run->user->name,
						'is_judge'  => Auth::check() ? ($score_run->user->id == Auth::user()->id) : 0,
						'id'       => $score_run->id,
						'deleted'  => $score_run->trashed()
						];
					$score_index = 0;
					foreach($score_run->scores as $score_element)
					{
						$thisRun['scores'][$score_index] = $score_element;
						$score_index++;
					}
					$challenge_list[$challenge_number]['runs'][] = $thisRun;
				}
				$challenge_list[$challenge_number]['has_scores'] = true;
			} else {
				$challenge_list[$challenge_number]['has_scores'] = false;
			}
		}

		//dd(DB::getQueryLog());
		View::share('title', $team->longname() . ' Scores');
		return View::make('display.teamscore', compact('team','challenge_list', 'grand_total', 'show_users'));
	}

	/**
	 * Sort Function for scores
	 *   Sorts by Score, Runs, then Aborts
	 * @param $a
	 * @param $b
	 * @return mixed
	 */
	public static function score_sort($a, $b) {
		// Sort by score first:
		if($a['total'] == $b['total']) {
			// Then by runs
			if($a['runs'] == $b['runs']) {
				// Then by aborts
				return $a['aborts'] - $b['aborts'];
			} else {
				return $a['runs'] - $b['runs'];
			}
		} else {
			return $b['total'] - $a['total'];
		}
	}

    public function compscore_top(Request $req, $competition_id, $csv = null)
    {
        return $this->compscore_actual($req, $competition_id, $csv, true);
    }

    public function compscore(Request $req, $competition_id, $csv = null)
    {
        return $this->compscore_actual($req, $competition_id, $csv, false);
    }

	public function compscore_actual(Request $req, $competition_id, $csv = null, $top = null)
	{
		$comp = Competition::with('divisions', 'divisions.teams', 'divisions.teams.school', 'divisions.challenges')->find($competition_id);
		$divisions = $comp->divisions;

        $timer = $this->init_timer();

		// Frozen Calculation
		$freeze_time = new Carbon($comp->freeze_time);
		if($comp->frozen AND isset($start_time->freeze_time)) {
			$frozen = true;
		} else {
			$frozen = false;
		}

		// Get score list and calculate totals
		$score_list = array();
		foreach($divisions as $division)
		{
			$score_list[$division->id] = array();
			$challenge_list = $division->challenges->pluck('id')->all();

			// Calculate the max score for each team and challenge
			$scores = DB::table('score_runs')
					->select('team_id', 'challenge_id',
						DB::raw('max(total) as chal_score'),
						DB::raw('count(total) as chal_runs'),
						DB::raw('sum(abort = 1) as aborts'))
					->groupBy('team_id', 'challenge_id')
					->orderBy('team_id', 'challenge_id')
					->where('division_id', $division->id)
					->whereNull('deleted_at')
					->whereIn('challenge_id', $challenge_list);  // Limit to currently attached challenges

			// If we're frozen, limit scores we count by the freeze time
			if($frozen) {
				$scores = $scores->where('run_time', '<=', $freeze_time->toTimeString())->get();
			} else {
				$scores = $scores->get();
			}

			// Just make force it to not be frozen
		    $frozen = false;

			// Sum up all of the scores by team
			foreach($scores as $score)
			{
				// Initialize the storage location for each team
				if(!array_key_exists($score->team_id, $score_list[$division->id])) {
					$score_list[$division->id][$score->team_id] = [
						'total'=> 0,
						'runs' => 0,
						'aborts' => 0
						];
				}
				$score_list[$division->id][$score->team_id]['total'] += $score->chal_score;
				$score_list[$division->id][$score->team_id]['runs'] += $score->chal_runs;
				$score_list[$division->id][$score->team_id]['aborts'] += $score->aborts;
			}


			// Find all of the teams with no scores yet and add them to the end of the list
			$team_list = $division->teams->pluck('id')->all();
			$missing_list = array_diff($team_list, array_keys($score_list[$division->id]));
			$score_list[$division->id] = $score_list[$division->id] + array_fill_keys($missing_list, [ 'total' => 0, 'runs' => 0, 'aborts' => 0 ] );

			// Descending sort by score
			//arsort($score_list[$division->id]);

			// Sort descening by Score, runs
			//    minus is a standin for the <=> operator
			//    a - b roughly equals a <=> b
			uasort($score_list[$division->id], 'self::score_sort');

			// Populate Places
			$place = 1;
			foreach($score_list[$division->id] as $team_id => $scores) {
				if($score_list[$division->id][$team_id]['runs'] > 0) {
					$score_list[$division->id][$team_id]['place'] = $place++;
				} else {
					// Teams with no runs have no place
					$score_list[$division->id][$team_id]['place'] = '-';
				}
			}
		}

		// Only show the top 3 scores from each division
		if($top) {
            foreach($score_list as $div => $div_scores) {
                $score_list[$div] = array_slice($div_scores, 0, 3, true);
            }
		}

		// CSV Output
		if($csv) {
			$output = "Division,Place,Team,School,Score,Runs,Aborts\n";

			foreach($divisions as $division) {
				foreach($score_list[$division->id] as $team_id => $score) {
					$output .= '"' . join('","', [
						$division->name,
						$score['place'],
						$division->teams->find($team_id)->name,
						$division->teams->find($team_id)->school->name,
						$score['total'],
						$score['runs'],
						$score['aborts']
					]) . "\"\n";
				}
			}

			$filename = str_replace(' ', '_', $comp->name) . '.csv';
			$headers = [
				'Content-Type' => 'text/csv',
				'Content-Disposition' => 'attachment; filename="' . $filename . '"'
			];

			return Response::make(rtrim($output, "\n"), 200, $headers);
		}


		$now = Carbon::now()->setTimezone('America/Los_Angeles');
		$event = new Carbon($comp->event_date);
		$display_timer = $now->isSameDay($event) || $req->input('display_timer', false);

		// Pull settings from the session variable
		$session_variable = "compsettings_$competition_id";
		$settings['columns'] = Session::get($session_variable . '_columns', 1);
		$settings['rows'] = Session::get($session_variable . '_rows', 15);
		$settings['delay'] = Session::get($session_variable . '_delay', 3000);
		$settings['font-size'] = Session::get($session_variable . '_font-size', 'x-large');

        if($top) {
            View::share('title', $comp->name . ' - Leading Teams');
        } else {
		    View::share('title', $comp->name . ' - Scores');
        }

		return View::make('display.compscore',
		                  compact('comp', 'divisions', 'score_list',
                                  'timer', 'frozen',  'display_timer',
							      'settings', 'top'));
	}

    public function compyearscore_top(Request $req,$compyear_id, $csv = null)
    {
        return $this->compyearscore_actual($req, $compyear_id, $csv, true);
    }

    public function compyearscore(Request $req,$compyear_id, $csv = null)
    {
        return $this->compyearscore_actual($req, $compyear_id, $csv, false);
    }


	public function compyearscore_actual(Request $req, $compyear_id, $csv = null, $top = null)
	{
		//Breadcrumbs::addCrumb('Statewide Score', 'compyearscore');

		$compyear = CompYear::with('competitions', 'divisions', 'divisions.teams', 'divisions.teams.school', 'divisions.challenges')->find($compyear_id);
		$divisions = $compyear->divisions;

		// Frozen Calculation
		$comp = $compyear->competitions->first();
		$freeze_time = new Carbon($comp->freeze_time);
		if($comp->frozen AND isset($start_time->freeze_time)) {
			$frozen = true;
		} else {
			$frozen = false;
		}

		// Just make force it to not be frozen
		$frozen = false;

		// Get score list and calculate totals
		$score_list = [];
		$teams = NULL;
		foreach($divisions as $division)
		{
			if($teams) {
				$teams = $teams->merge($division->teams);
			} else {
				$teams = $division->teams;
			}

			//echo '<pre>' . print_r($teams->pluck('name')->all(),true) . '</pre>';

			if(!array_key_exists($division->level, $score_list)) {
				$score_list[$division->level] = [];
			}
			$challenge_list = $division->challenges->pluck('id')->all();

			// Calculate the max score for each team and challenge
			$scores = DB::table('score_runs')
				->select('team_id', 'challenge_id',
					DB::raw('max(total) as chal_score'),
					DB::raw('count(total) as chal_runs'),
					DB::raw('sum(abort = 1) as aborts'))
				->groupBy('team_id', 'challenge_id')
				->orderBy('team_id', 'challenge_id')
				->where('division_id', $division->id)
				->whereNull('deleted_at')
				->whereIn('challenge_id', $challenge_list);  // Limit to currently attached challenges

			// If we're frozen, limit scores we count by the freeze time
			if($frozen) {
				$scores = $scores->where('run_time', '<=', $freeze_time->toTimeString())->get();
			} else {
				$scores = $scores->get();
			}

			// Sum up all of the scores by team
			foreach($scores as $score)
			{
				// Initialize the storage location for each team
				if(!array_key_exists($score->team_id, $score_list[$division->level])) {
					$score_list[$division->level][$score->team_id] = [
						'total'=> 0,
						'runs' => 0,
						'aborts' => 0
					];
				}
				$score_list[$division->level][$score->team_id]['total'] += $score->chal_score;
				$score_list[$division->level][$score->team_id]['runs'] += $score->chal_runs;
				$score_list[$division->level][$score->team_id]['aborts'] += $score->aborts;
			}


			// Find all of the teams with no scores yet and add them to the end of the list
			$team_list = $division->teams->pluck('id')->all();
			$missing_list = array_diff($team_list, array_keys($score_list[$division->level]));
			$score_list[$division->level] =
				$score_list[$division->level] +
				array_fill_keys($missing_list, [ 'total' => 0, 'runs' => 0, 'aborts' => 0 ] );

			// Sort descening by Score, runs
			//    minus is a standin for the <=> operator
			//    a - b roughly equals a <=> b
			uasort($score_list[$division->level], 'self::score_sort');

			// Populate Places
			$place = 1;
			foreach($score_list[$division->level] as $team_id => $scores) {
				if($score_list[$division->level][$team_id]['runs'] > 0) {
					$score_list[$division->level][$team_id]['place'] = $place++;
				} else {
					// Teams with no runs have no place
					$score_list[$division->level][$team_id]['place'] = '-';
				}
			}
		}

		// Only show the top 3 scores from each division
		if($top) {
            foreach($score_list as $div => $div_scores) {
                $score_list[$div] = array_slice($div_scores, 0, 3, true);
            }
		}

		// CSV Output
		if($csv) {
			$output = "Division,Place,Team,School,Score,Runs,Aborts\n";

			foreach($score_list as $level => $scores) {
				foreach($scores as $team_id => $score) {
					$output .= '"' . join('","', [
						'Division ' . $level,
						$score['place'],
						$teams->find($team_id)->name,
						$teams->find($team_id)->school->name,
						$score['total'],
						$score['runs'],
						$score['aborts']
					]) . "\"\n";
				}
			}

			$filename = str_replace(' ', '_', 'RoboPlay Competition ' . $compyear->year) . '.csv';
			$headers = [
				'Content-Type' => 'text/csv',
				'Content-Disposition' => 'attachment; filename="' . $filename . '"'
			];

			return Response::make(rtrim($output, "\n"), 200, $headers);
		}

        // Determine if timer should be displayed
		$now = Carbon::now()->setTimezone('America/Los_Angeles');
		$event = new Carbon($comp->event_date);
		$display_timer = $now->isSameDay($event) || $req->input('display_timer', false);

        $timer = $this->init_timer();

		// Pull settings from the session variable
		$session_variable = "compyearsettings_$compyear_id";
		$settings['columns'] = Session::get($session_variable . '_columns', 1);
		$settings['rows'] = Session::get($session_variable . '_rows', 15);
		$settings['delay'] = Session::get($session_variable . '_delay', 3000);
		$settings['font-size'] = Session::get($session_variable . '_font-size', 'x-large');

        if($top) {
            View::share('title', 'RoboPlay ' . $compyear->year . ' - Leading Teams');
        } else {
		    View::share('title', 'RoboPlay ' . $compyear->year . ' - Scores');
		}

		return View::make('display.compyearscore',
		                compact('compyear', 'teams', 'score_list',
								'timer', 'frozen', 'display_timer',
								'settings', 'top'));
	}

	public function all_scores(Request $req, $compyear_id)
	{
		// TODO: Synchronize Cache across multiple clients
		// TODO: Display "Last Generated" or "Last Fetched" Time for Scores

		$compyear = Cache::remember("all_scores_compyear_$compyear_id", 5 * 60, function() use ($compyear_id) {
			return CompYear::with('competitions', 'divisions', 'divisions.teams', 'divisions.teams.school', 'divisions.challenges')
							->find($compyear_id);
		});

		$divisions = $compyear->divisions;

		// Frozen Calculation
		$comp = $compyear->competitions->first();
		$freeze_time = new Carbon($comp->freeze_time);
		if($comp->frozen AND isset($freeze_time)) {
			$frozen = true;
		} else {
			$frozen = false;
		}

		if($req->has('clear_cache')) {
			Cache::flush("all_scores_score_list_$compyear_id");
}

		// Get score list and calculate totals
		$score_list = Cache::remember("all_scores_score_list_$compyear_id", 5 * 60, function() use ($divisions, $frozen, $freeze_time){
			$score_list = [];
			$teams = NULL;
			foreach($divisions as $division)
			{
				if($teams) {
					$teams = $teams->merge($division->teams);
				} else {
					$teams = $division->teams;
				}

				$challenge_list = $division->challenges->pluck('id')->all();

				// Calculate the max score for each team and challenge
				$scores = DB::table('score_runs')
					->select('team_id', 'challenge_id',
						DB::raw('max(total) as chal_score'),
						DB::raw('count(total) as chal_runs'),
						DB::raw('sum(abort = 1) as aborts'))
					->groupBy('team_id', 'challenge_id')
					->orderBy('team_id', 'challenge_id')
					->where('division_id', $division->id)
					->whereNull('deleted_at')
					->whereIn('challenge_id', $challenge_list);  // Limit to currently attached challenges

				// If we're frozen, limit scores we count by the freeze time
				if($frozen) {
					$scores = $scores->where('run_time', '<=', $freeze_time->toTimeString())->get();
				} else {
					$scores = $scores->get();
				}

				// Sum up all of the scores by team
				foreach($scores as $score)
				{
				    $team = $teams->find($score->team_id);

					// Initalize the storage location for each team
					if(!array_key_exists($team->id, $score_list)) {
						$score_list[$team->id] = [
							'school' => $team->school->name,
							'name' => $team->name,
							'total' => 0,
							'runs' => 0,
							'aborts' => 0
						];
					}
					$score_list[$team->id]['total'] += $score->chal_score;
					$score_list[$team->id]['runs'] += $score->chal_runs;
					$score_list[$team->id]['aborts'] += $score->aborts;
				}


				// Find all of the teams with no scores yet and add them to the end of the list
				$team_list = $division->teams->pluck('id')->all();
				$missing_list = array_diff($team_list, array_keys($score_list));
				foreach($missing_list as $missing_team) {
				    $team = $teams->find($missing_team);
					$score_list[$team->id] = [
						'school' => $team->school->name,
						'name' => $team->name,
						'total' => 0,
						'runs' => 0,
						'aborts' => 0
					];
			    }
			}

			// Sort by school name, then by team name
			uasort($score_list, 'self::score_sort');
			return $score_list;
		});

		$now = Carbon::now()->setTimezone('America/Los_Angeles');
		$event = new Carbon($comp->event_date);
		$display_timer = $now->isSameDay($event) || $req->input('display_timer', false);

		$timer = $this->init_timer();

		// Pull settings from the session variable
		$session_variable = "all_scores_settings_$compyear_id";
		$settings['columns'] = Session::get($session_variable . '_columns', 1);
		$settings['rows'] = Session::get($session_variable . '_rows', 15);
		$settings['delay'] = Session::get($session_variable . '_delay', 3000);
		$settings['font-size'] = Session::get($session_variable . '_font-size', 'x-large');

	    View::share('title', 'RoboPlay ' . $compyear->year . ' - All Scores');

		return View::make('display.allscore',
		                compact('compyear', 'comp', 'teams', 'score_list',
								'timer', 'frozen', 'freeze_time', 'display_timer',
								'settings'));
	}

	public function attempts($compyear_id)
	{
	    $compyear = CompYear::with('divisions')
		                    ->find($compyear_id);

	    // get the graph data
	    foreach([ 1, 2, 3] as $level) {
	        $data = $this->attempts_data($compyear_id, $level);
            $chart_data[$level] = $data['chart_data'];
            $max_data[$level] = $data['max_data'];
        }

        $now = Carbon::now()->setTimezone('America/Los_Angeles');
		//$event = new Carbon($comp->event_date);
		$display_timer = true; //$now->isSameDay($event) || $req->input('display_timer', false);

		// Event Timing
		$start_time = Carbon::now()->setTimezone('America/Los_Angeles')->toTimeString();
		$this_event = Schedule::where('start', '<', $start_time)->orderBy('start', 'DESC')->first();
		$next_event = Schedule::where('start', '>', $start_time)->orderBy('start')->first();
		$frozen = false;

		// Pull settings from the session variable
		$session_variable = "all_scores_settings_$compyear_id";
		$settings['columns'] = Session::get($session_variable . '_columns', 1);
		$settings['rows'] = Session::get($session_variable . '_rows', 15);
		$settings['delay'] = Session::get($session_variable . '_delay', 3000);
		$settings['font-size'] = Session::get($session_variable . '_font-size', 'x-large');

	    View::share('title', 'RoboPlay ' . $compyear->year . ' All Scores');
//ddd($chart_data, $max_data);
		return View::make('display.attempts',
		                compact('compyear', 'chart_data', 'max_data',
		                        'this_event', 'next_event',
								'frozen', 'start_time', 'display_timer',
								'settings'));

	}

	public function attempts_data($compyear_id, $level, $json_object = false)
	{
	    $compyear = CompYear::with('divisions', 'divisions.competition',
	                               'competitions', 'competitions.divisions', 'competitions.divisions.challenges')
		                    ->find($compyear_id);

    	//$challenge_list = new Illuminate\Database\Eloquent\Collection();
    	$max_data = [];
        $data = [];
    	foreach($compyear->competitions as $competition)
		{
		    foreach($competition->divisions as $division) {
		        $challenge_list = $division->challenges->pluck('display_name', 'id')->all();

		        if($division->level == $level) {
        			// Count attempts
        			$scores = DB::table('score_runs')
        					->select('challenge_id',  DB::raw('count(*) as runs'), DB::raw('max(total) as max'))
        					->groupBy('challenge_id')
        					->where('division_id', $division->id)
        					->whereNull('deleted_at')
        					->whereIn('challenge_id', array_keys($challenge_list))
        					->get();

                    // Build Score Object
        			$obj = new \stdClass;
        			$obj->type = "stackedBar";
        			$obj->legendText = $competition->location;
        			$obj->showInLegend = "true";
        			$obj->color = $competition->color;
        			$obj->dataPoints = [];
        		    foreach($scores as $score) {
        		        $scoreObj = new \stdClass;
        		        $scoreObj->label = trim($division->challenges->find($score->challenge_id)->display_name);
        		        $scoreObj->y = $score->runs;
        		        $scoreObj->order = $division->challenges->find($score->challenge_id)->pivot->display_order;
        		        $obj->dataPoints[] = $scoreObj;
        		        if(array_key_exists($score->challenge_id, $challenge_list)) {
        		            unset($challenge_list[$score->challenge_id]);
        		        }

                        if(array_key_exists($score->challenge_id, $max_data)) {
                            $max_data[$score->challenge_id]['max_score'] = max($score->max, $max_data[$score->challenge_id]['max_score']);
                        } else {
                            $max_data[$score->challenge_id]['max_score'] = $score->max;
        		            $max_data[$score->challenge_id]['max_possible'] = $division->challenges->find($score->challenge_id)->points;
        		            $max_data[$score->challenge_id]['order'] = $scoreObj->order;
        		            $max_data[$score->challenge_id]['name'] = $scoreObj->label;
                        }

        		    }

        		    foreach($challenge_list as $challenge_id => $name) {
           		        $scoreObj = new \stdClass;
        		        $scoreObj->label = trim($name);
        		        $scoreObj->y = 0;
        		        $scoreObj->order = $division->challenges->find($challenge_id)->pivot->display_order;
        		        $obj->dataPoints[] = $scoreObj;

        		        $max_data[$challenge_id]['max_score'] = 0;
        		        $max_data[$challenge_id]['max_possible'] = $division->challenges->find($challenge_id)->points;
        		        $max_data[$challenge_id]['order'] = $scoreObj->order;
        		        $max_data[$challenge_id]['name'] = $scoreObj->label;
        		    }

        		    usort($obj->dataPoints, function($a, $b) {
        		        return $b->order - $a->order;
        		    });

        		    uasort($max_data, function($a, $b) {
        		        return $a['order'] - $b['order'];
        		    });

        		    $data[] = $obj;
        		}
        	}
		}

		$chart_data = new \stdClass;
		$chart_data->title = new \stdClass;
		$chart_data->title->text = "Division $level Attempts";
		$chart_data->axisY = new \stdClass;
		$chart_data->axisY->interval = 20;
		$chart_data->data = $data;

		return [ 'chart_data' => json_encode($chart_data),
		         'max_data' => $max_data ];
	}

	public function delete_score($team_id, $score_run_id)
	{
		$score_run = Score_run::find($score_run_id);
		if(Roles::isAdmin() OR $score_run->user_id == Auth::user()->id) {
			$score_run->delete();
			return redirect()->route('display.teamscore', [ $team_id ])->with('message', 'Score Deleted');
		}
		return redirect()->route('display.teamscore', [ $team_id ])->with('message', 'You do not have permission to delete this score.');
	}

	public function restore_score($team_id, $score_run_id)
	{
		$score_run = Score_run::withTrashed()->find($score_run_id);

		// Allow Admins or the user who deleted the scores to restore them
		if(Roles::isAdmin() or $score_run->user_id == Auth::user()->id ) {
			$score_run->restore();
			return redirect()->route('display.teamscore', [ $team_id ])->with('message', 'Score Restored');
		}
		return redirect()->route('display.teamscore', [ $team_id ])->with('message', 'You do not have permission to restore this score.');
	}

	public function challenge_students_csv() {
		$content = 'School,Team,"Student Name"' . "\n";

		$comps = Competition::with('divisions')->where('name', 'not like', DB::raw('"%test%"'))->get();
		$div_list = [];
		foreach($comps as $comp) {
			$div_list = array_merge($div_list, $comp->divisions->pluck('id')->all());
		}
		$teams = Team::with('school')->whereIn('division_id', $div_list)->get();

		foreach($teams as $team) {
			foreach($team->student_list() as $student) {
				$content .= '"' . $team->school->name . '","' . $team->name	. '","' . $student . "\"\n";
			}
		}
		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="challenge_student.csv'
		));
	}

	public function video_students_csv() {
		$content = 'Division,School,Video,"Student Name"' . "\n";

		$comps = Vid_competition::with('divisions')->where('name', 'not like', DB::raw('"%test%"'))->get();
		$div_list = [];
		foreach($comps as $comp) {
			$div_list = array_merge($div_list, $comp->divisions->pluck('id')->all());
		}
		$videos = Video::with('school', 'vid_division')->whereIn('vid_division_id', $div_list)->get();

		foreach($videos as $video) {
			foreach($video->student_list() as $student) {
				$content .= '"' . $video->vid_division->name . '","'. $video->school->name . '","' . $video->name	. '","' . $student . "\"\n";
			}
		}
		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="video_student.csv'
		));
	}

	public function video_list($competition_id, $winners = false)
	{
		$comp = Vid_competition::with('divisions')->find($competition_id);

		//Breadcrumbs::addCrumb($comp->name . ' Videos', route('display.video_list', [ $competition_id ]));
		View::share('title', $comp->name . ' | Video List');

		//dd(DB::getQueryLog());
		$divs = [0];
		foreach($comp->divisions as $div) {
			$divs[] = $div->id;
		}

		$video_query = Video::with('school', 'vid_division', 'awards')
		                   ->where('flag', VideoFlag::Normal)
		                   ->whereIn('vid_division_id', $divs);
		if($winners) {
		    $video_query = $video_query->has('awards');
		}

        $video_list = $video_query->get();

		$videos = [];
		foreach($video_list as $video) {
			$videos[$video->vid_division->name][$video->name] = $video;
		}

		return View::make('display.video_list', compact('videos', 'comp', 'winners'));
	}

	public function show_video($competition_id, $video_id)
	{
		//Breadcrumbs::addCrumb('Video List', route('display.video_list', [ $competition_id ]));
		//Breadcrumbs::addCrumb('Video', '');
		View::share('title', 'Show Video');

		$video = Video::find($video_id);
		if(empty($video)) {
			// Invalid video
			return redirect()->route('display.show_videos', [ $competition_id ])
							->with('message', "Invalid video id '$video_id'.  Video no longer exists or another error occured.");
		}

		return View::make('display.show_video', compact('video'));
	}

	public function compsettings(Request $req, $competition_id) {
		$session_variable = "compsettings_$competition_id";
		$req->session()->put($session_variable . '_columns', $req->input('columns', 1));
		$req->session()->put($session_variable . '_rows', $req->input('rows', 15));
		$req->session()->put($session_variable . '_delay', $req->input('delay', 3000));
		$req->session()->put($session_variable . '_font-size', $req->input('font-size', 'x-large'));

		return redirect()->route('display.compscore', [ $competition_id ]);
	}

	public function compyearsettings(Request $req, $compyear_id) {
		$session_variable = "compyearsettings_$compyear_id";
		$req->session()->put($session_variable . '_columns', $req->input('columns', 1));
		$req->session()->put($session_variable . '_rows', $req->input('rows', 15));
		$req->session()->put($session_variable . '_delay', $req->input('delay', 3000));
		$req->session()->put($session_variable . '_font-size', $req->input('font-size', 'x-large'));

		return redirect()->route('display.compyearscore', [ $compyear_id ]);
	}

	public function all_scores_settings(Request $req, $compyear_id) {
		$session_variable = "all_scores_settings_$compyear_id";
		$req->session()->put($session_variable . '_columns', $req->input('columns', 1));
		$req->session()->put($session_variable . '_rows', $req->input('rows', 15));
		$req->session()->put($session_variable . '_delay', $req->input('delay', 3000));
		$req->session()->put($session_variable . '_font-size', $req->input('font-size', 'x-large'));

		return redirect()->route('display.all_scores', [ $compyear_id ]);
	}

	public function export_year_scores($year) {
	    $compyear = CompYear::with('divisions', 'divisions.teams', 'divisions.challenges', 'divisions.competition')->where('year', $year)->first();
		$divisions = $compyear->divisions;
		$division_list = $divisions->pluck('id')->all();

		$content = "Division,Challenge,Team,Location,Run,Score,s1,s2,s3,s4,s5,s6,Used\n";

		foreach ($divisions as $division) {
		    $division_id = $division->id;

            // Only return scores from this division
            // Overcome limitation of Eloquent
            $division->challenges = $division->challenges->filter( function($val) use ($division_id)
							{
								return ($val->pivot->division_id === $division_id);
							});

			foreach ($division->challenges as $challenge) {
			    foreach ($challenge->scores as $score) {
    			    $line = [];
    			    $team = $division->teams->find($score->team_id);
    			    if(!$team) {
    			        continue;
    			    }

    			    $challenge_number = $challenge->pivot->display_order;
    			    $num = sprintf('%02d ', $challenge_number);

    			    $line = [
    			              $division->name,
    			              $num . $challenge->display_name,
    			              $team->name,
    			              $division->competition->location,
    			              $score->run_number,
    			              $score->total
    			            ];
                    $used = 0;
                    foreach ($score->scores as $points) {
                        $line[] = $points;
                        if($points != '-' and $points > 0) {
                            $used = $used + 1;
                        }
                    }
                    $line[] = $used;

    			    $content .= join($line, ',') . "\n";
			    }
			}
	    }

	    // return an string as a file to the user
		return Response::make($content, '200', [
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="raw_scores_' . $year . '.csv"'
		]);
	}
}
