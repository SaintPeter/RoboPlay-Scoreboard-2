<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\Models\ {
	CompYear,
	Score_run
};
use Carbon\Carbon;

class ScoreApiController extends Controller
{
	public function challenges(Request $req, $year, $level) {
		$cache_name = "challenge_data_${year}_${level}";
		if($req->has('clear_cache')) {
			Cache::delete($cache_name);
		}

		$challenge_data = Cache::remember($cache_name, 60 * 24, function() use ($year, $level) {
			$compyear = CompYear::where('year',$year)
				->with([ 'divisions' => function($q) use ($level) {
					return $q->where('level', $level);
				}, 'divisions.challenges',
					'divisions.challenges.randoms',
					'divisions.challenges.score_elements',
					'divisions.challenges.random_lists'])
				->first();

			return $compyear->divisions->first()->challenges->map(function($challenge) {
					return $challenge
						->only([ 'id',
								 'display_name',
								 'display_order',
								 'rules',
								 'points',
							     'randoms',
								 'score_elements',
								 'random_lists']);
				});

		});

		return response()->json($challenge_data);
	}

	public function save_scores(Request $req) {
		$score_runs = $req->input('scores', []);
		$completed = [];
		foreach($score_runs as $run) {
			// TODO: Set Global Constant for Score Element count
			$scores = array_fill(1, 6, '-');
			$total = 0;
			if($run['abort']) {
				for($i = 1; $i <= $run['elementCount']; $i++) {
					$scores[$i] = 'A';
				}
			} else {
				foreach($run['scores'] as $index => $value) {
					$scores[$index] = $value;
					$total += $value;
				}
				$total = max(0, $total);
			}

			try {
				Score_run::create([
					"run_number" => $run['timestamp'],
					"run_time" => Carbon::createFromTimestamp($run['timestamp']),
					"scores" => $scores,
					"total" => $total,
					"abort" => $run['abort'],
					"judge_id" => Auth::user()->id,
					"team_id" => $run['teamId'],
					"challenge_id" => $run['chalId'],
					"division_id" => $run['divId']
				]);
				$completed[] = $run['timestamp'];
			} catch(\Exception $exception) {
				// Ignore errors
			}
		}
		return \Response::json($completed);
	}

	public function team_runs(Request $req, $team_id) {
		$runs = DB::select(
			'SELECT team_id, challenge_id, COUNT(*) AS runs, SUM(abort = 1) AS aborts ' .
			'FROM scoreboard2.score_runs ' .
			'WHERE team_id = ? ' .
			'GROUP BY team_id , challenge_id'
		, [ $team_id ]);
		return \Response::json($runs);
	}
}
