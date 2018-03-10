<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CompYear;

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
}
