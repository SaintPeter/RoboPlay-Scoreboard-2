<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use View;
use App\Helpers\Roles;
use App\Models\{Competition, Team, JudgeAwards, JudgeNominations, CompYear, Division};

class AwardsController extends Controller
{
    public function index($div_id) {
		$div = Division::with(['teams' => function($query) {
				return $query->has('nominations');
			}, 'teams.school', 'teams.nominations','teams.nominations.judge', 'teams.awards'])
			->with('competition')
			->find($div_id);

		$awards = JudgeAwards::all()->keyBy('col')->toArray();

		$nom_list = [];
		foreach($awards as $award) {
			$nom_list[$award['name']] = [
				'teams' => [],
				'award_id' => $award['id'],
				'awarded' => false
			];
		}

		foreach($div->teams as $team) {
			foreach($team->nominations as $nom) {
				foreach($awards as $col => $award) {
					if($nom[$col]) {
						$nom_list[$award['name']]['teams'][$team->id] = $team;
						if($team->has_award($award['id'])) {
							$nom_list[$award['name']]['awarded'] = true;
						}
					}
				}
			}
		}

		$is_admin = Roles::isAdmin();

	    View::share('title', 'Judge Award Nominations');
		View::share('subtitle', $div->competition->name . " - " . $div->name);
		return view('awards.index')->with(compact('div_id', 'nom_list', 'is_admin'));

    }

    public function list($comp_id) {
    	$comp = Competition::with(['divisions', 'divisions.teams' => function($query) {
	            return $query->has('awards');
		    }], 'divisions.teams.awards', 'divisions.teams.school')
		    ->findOrFail($comp_id);
    	
    	$divs = [];
    	foreach($comp->divisions as $div) {
    		foreach($div->teams as $team) {
    			foreach($team->awards as $award) {
    				if(!array_key_exists($div->name, $divs)) {
					    $divs[$div->name] = [];
				    }
				    $divs[$div->name][$award->name] = $team;
			    }
		    }
	    }

	    View::share('title', 'Judge Award List');
	    View::share('subtitle', $comp->name);
		return view('awards.list')->with(compact('divs','comp'));
    }

    public function grant($div_id, $team_id, $award_id) {
    	$team = Team::with('awards')->find($team_id);
    	$team->awards()->attach($award_id);

    	return redirect()->route('awards.index', $div_id);
    }

	public function revoke($div_id, $team_id, $award_id) {
		$team = Team::with('awards')->find($team_id);
		$team->awards()->detach($award_id);

		return redirect()->route('awards.index', $div_id);
	}
}
