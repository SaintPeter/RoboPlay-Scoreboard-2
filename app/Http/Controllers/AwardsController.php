<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ {
	Team, JudgeAwards, JudgeNominations, CompYear, Division
};
use View;

class AwardsController extends Controller
{
    public function index($year = null, $comp = null, $div = null) {
		$div = Division::with(['teams' => function($query) {
				return $query->has('nominations');
			}, 'teams.school', 'teams.nominations','teams.nominations.judge'])
			->find($div);

		$nom_list = [
			'Spirit Award' => [],
			'Teamwork Award' => [],
			'Perseverance Award' => []
		];

		foreach($div->teams as $team) {
			foreach($team->nominations as $nom) {
				if($nom->spirit) {
					$nom_list['Spirit Award'][$team->id] = $team;
				}
				if($nom->teamwork) {
					$nom_list['Teamwork Award'][$team->id] = $team;
				}
				if($nom->persevere) {
					$nom_list['Perseverance Award'][$team->id] = $team;
				}
			}
		}

	    View::share('title', 'Judge Awards');
		return view('awards.index')->with(compact('div', 'nom_list'));

    }
}
