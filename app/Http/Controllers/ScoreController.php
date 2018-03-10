<?php

namespace App\Http\Controllers;

use View;
use Auth;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Models\ {
    Team,
    Division,
    Challenge,
    Score_run,
    Competition
};

define('SCORE_COLUMNS', 6);

class ScoreController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @param int $competition_id
	 * @param int $division_id
	 * @param int $team_id
	 * @return \Illuminate\Http\Response
	 */
	public function index($competition_id = 0, $division_id = 0, $team_id = 0)
	{
		if($competition_id == 0) {
			$competitions = Competition::where('active', 1)->get();
			return View::make('score.competition_list', compact('competitions'));
		}

		if($division_id == 0) {
			$divisions = Division::where('competition_id', $competition_id)->get();
			return View::make('score.division_list', compact('divisions', 'competition_id'));
		}

		if($team_id == 0) {
			$teams = Team::where('division_id', $division_id)->with('school')->get();
			return View::make('score.team_list', compact('teams', 'division_id', 'competition_id'));
		}

		$challenges = Division::with('challenges', 'challenges.score_elements')
								->find($division_id)->challenges;
		$team = Team::find($team_id);
		$user = Auth::user();

		if(empty($team))
		{
			$teams = Team::where('division_id', $division_id)->get();
			return View::make('score.team_list', compact('teams', 'division_id', 'competition_id'));
		}

        return View::make('score.index')
        			->with(compact('challenges', 'team_id', 'team', 'user', 'competition_id', 'division_id'));
	}

	public function doscore($team_id, $challenge_id)
	{
		$challenge = Challenge::with('score_elements','randoms','random_lists','divisions')->find($challenge_id);
		$team = Team::with('division', 'division.competition')->find($team_id);
		$user = Auth::user();

		if(!$challenge->divisions->contains($team->division_id)) {
			return "Could not find that challenge for that team";
		}

		$run_number = Score_run::where('team_id',  $team_id)->where('challenge_id', $challenge_id)->max('run_number') + 1;

		//ddd($challenge->random_lists->first()->get_formatted());

		return View::make('score.doscore')
					->with(compact('challenge', 'team', 'run_number', 'user'))
					->with('competition_id', $team->division->competition->id)
					->with('division_id', $team->division->id)
					->with('score_elements', $challenge->score_elements);

	}

	// Take a list of input values and turn them into scores
	function calculate_scores($value_list, $challenge_id)
	{
		$challenge = Challenge::with('score_elements')->find($challenge_id);

		$scores = array_fill(1, SCORE_COLUMNS, '-');
		$total = 0;
		foreach($challenge->score_elements as $se) {
			if($se->type == 'score_slider') {
				// With a score slider the multiplier is always 1
				$scores[$se->element_number] = $se->base_value + intval($value_list[$se->id]['value']);
			} else {
				// Everything else uses the multiplier
				$scores[$se->element_number] = $se->base_value + (intval($value_list[$se->id]['value']) * $se->multiplier);
			}
			$total += $scores[$se->element_number];
		}
		$total = max($total, 0);

		return array($scores, $total);
	}

	// Return an array with aborts filled in
	public function abort_scores($challenge_id) {
		$challenge = Challenge::with('score_elements')->find($challenge_id);

		$scores = array_fill(1, SCORE_COLUMNS, '-');
		foreach($challenge->score_elements as $se) {
			$scores[$se->element_number] = 'A';
		}
	 return $scores;
	}

	public function save(Request $req, $team_id, $challenge_id)
	{
		$team = Team::find($team_id);
		if($req->has('cancel')) {
			return redirect()->route('score.score_team', ['team_id' => $team_id, 'competition_id' => $team->division->competition->id, 'divison_id' => $team->division_id])
				->with('message', 'Did not Score');
		}
		$challenge = Challenge::with('score_elements','divisions')->find($challenge_id);

		if(!$challenge->divisions->contains($team->division_id)) {
			return View::make('score.doscore')
					->with(compact('challenge', 'team', 'run_number'))
					->with('score_elements', $challenge->score_elements)
					->with('message', "Cannot find that Division/Team Combination")
					->withInput();
		}

		if($req->has('abort')) {
			$total = 0;
			$scores = $this->abort_scores($challenge_id);
		} else {
			$value_list = $req->input('scores', array());
			list($scores, $total) = $this->calculate_scores($value_list, $challenge_id);
		}

		$run_number = Score_run::where('team_id',  $team_id)->where('challenge_id', $challenge_id)->max('run_number') + 1;

		$date = Carbon::now('UTC')->setTimeZone("PDT");

		$newRun = array('run_number' => $run_number,
						'scores' => $scores,
						'total' => $total,
						'team_id' => $team_id,
						'user_id' => Auth::user()->id,
						'challenge_id' => $challenge_id,
						'division_id' => $team->division_id,
						'run_time' => $date->format('H:i:s'));

		Score_run::create($newRun);

		return redirect()->route('score.score_team', [
			'team_id' => $team_id,
			'competition_id' => $team->division->competition->id,
			'division_id' => $team->division_id]);
	}

	public function scorer (Request $req) {

		if($req->has('clear_cache')) {
			Cache::delete('competition_list');
		}

		$competition_list = Cache::remember('competition_list',24*60, function() {
			$comps = Competition::with('divisions','divisions.teams', 'divisions.teams.school')->where('active',1)->get();

			return json_encode(
				$comps->reduce(function ($compOutput, $comp) {
					$compOutput[$comp->id] = [
						'name' => $comp->name,
						'year' => $comp->event_date->year,
						'divisions' => $comp->divisions->reduce(function ($divOutput, $div) {
							$divOutput[$div->id] = [
								'name' => $div->name,
								'level' => $div->level,
								'teams' => $div->teams->reduce(function ($teamOutput, $team) {
									$teamOutput[$team->id] = $team->name . ' (' . $team->school->name . ')';
									return $teamOutput;
								}, [])
							];
							return $divOutput;
						}, [])
					];
					return $compOutput;
				}, [])
			);
		});

		// Judge Info
		$judgeName = Auth::user()->name;
		$judgeId = Auth::user()->id;

		return View::make('scorer.index')->with(compact('competition_list','judgeName', 'judgeId'));
	}
}
