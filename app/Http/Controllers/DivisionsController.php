<?php

namespace App\Http\Controllers;

use DB;
use View;
use Session;
use Validator;
use App\Helpers\Roles;
use Illuminate\Http\Request;

use App\ {
    Models\Challenge,
    Models\Score_run,
    Models\Competition,
    Models\Division
};
class DivisionsController extends Controller {

	/**
	 * Division Repository
	 *
	 * @var Division
	 */
	protected $division;

	public function __construct(Division $division)
	{
		parent::__construct();
		$this->division = $division;
		//Breadcrumbs::addCrumb('Competition Divisions', route('divisions.index'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$divisions = $this->division
			->with('competition', 'challenges')
			->orderBy(DB::raw('YEAR(created_at)'),'desc')
			->orderBy('competition_id')
			->orderBy('display_order')
			->get();

		View::share('title', 'Competition Divisions');
		return View::make('divisions.index', compact('divisions'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//Breadcrumbs::addCrumb('Add Division', route('divisions.create'));
		View::share('title', 'Add Competition Division');

		$competitions = Competition::orderBy(DB::raw('YEAR(event_date)'),'desc')->pluck('name','id')->all();

		return View::make('divisions.create')
				   ->with('competitions', $competitions);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $req
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $req)
	{
		$input = $req->all();
		$validation = Validator::make($input, Division::$rules);

		if ($validation->passes())
		{
			$this->division->create($input);

			return redirect()->route('divisions.index');
		}

		return redirect()->route('divisions.create')
			->withInput()
			->withErrors($validation)
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
		//Breadcrumbs::addCrumb('Show Division', route('divisions.show', $id));
		View::share('title', 'Show Division');
		$division = $this->division->with('competition','challenges','challenges.score_elements')->findOrFail($id);
		$challenges = $division->challenges;
		$division_list = Division::longname_array_counts();

		return View::make('divisions.show', compact('division', 'challenges', 'division_list'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//Breadcrumbs::addCrumb('Edit Division', route('divisions.edit', $id));
		View::share('title', 'Edit Division');
		$division = $this->division->find($id);

		if (is_null($division))
		{
			return redirect()->route('divisions.index');
		}

		$competitions = Competition::pluck('name','id')->all();

		return View::make('divisions.edit', compact('division'))
				   ->with('competitions', $competitions);
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
		$input = array_except($req->all(), '_method');
		$validation = Validator::make($input, Division::$rules);

		if ($validation->passes())
		{
			$division = $this->division->find($id);
			$division->update($input);

			return redirect()->route('divisions.index');
		}

		return redirect()->route('divisions.edit', $id)
			->withInput()
			->withErrors($validation)
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
		$this->division->find($id)->delete();

		return redirect()->route('divisions.index');
	}

	/**
	 * List all challenges and assign them to the given division
	 *
	 * @param  int  $division_id
	 * @return \Illuminate\Http\Response
	 */
	public function assign($division_id)
	{
		//Breadcrumbs::addCrumb('Show Division', route('divisions.show', $division_id));
		//Breadcrumbs::addCrumb('Assign Challenges', '');
		View::share('title', 'Assign Challenges');

		// Setup challenge query
		$challenge_query = Challenge::with('divisions');

		// Selected year, level select set in filters.php -> App::before()
		$year = Session::get('year', false);
		$level_select= Session::get('level_select', false);

		// Filter on selected year
		if($year) {
			$challenge_query = $challenge_query->where('year', $year);
		}

		// Filter on Level, if set
		if($level_select) {
			$challenge_query = $challenge_query->where('level', $level_select);
		}

		// Get challenges
	 	$all_challenges = $challenge_query->get();

		$all_list = [];
		$selected_list = [];

		$all_list = $all_challenges->pluck('internal_name', 'id')->all();


	 	foreach($all_challenges as $challenge) {
	 		if($challenge->divisions->contains($division_id)) {
	 			$selected_list[] =  $challenge->id;
	 		}
	 	}

	 	return View::make('divisions.assign', compact('all_list', 'selected_list', 'division_id'));
	}

	public function saveassign(Request $req)
	{
		$has_list = $req->input('has', array());
		$division_id = $req->input('division_id', 0);

		$division = Division::with('challenges')->find($division_id);

		$order = 1;
		foreach($has_list as $challenge_id) {
			$update[$challenge_id] = array('display_order' => $order);
			$order++;
		}

		$division->challenges()->sync($update);

		return redirect()->route('divisions.show', $division_id);
	}

	public function removeChallenge($division_id, $challenge_id)
	{
		$division = Division::with('challenges')->findOrFail($division_id);

		$division->challenges()->detach($challenge_id);

		$has_list = $division->challenges->pluck('id')->all();

		$order = 1;
		foreach($has_list as $id) {
			if($challenge_id != $id) {
				$update[$id] = array('display_order' => $order);
				$order++;
			}
		}

		$division->challenges()->sync($update);

		return redirect()->route('divisions.show', $division_id);
	}

	// Reorder the challenges to match those passed in via POST
	public function updateChallengeOrder($division_id)
	{
		$challenge_list = $req->input('challenge', array());
		$division = Division::with('challenges', 'challenges.score_elements')->findOrFail($division_id);

		$order = 1;
		foreach($challenge_list as $challenge_id) {
			$update[$challenge_id] = array('display_order' => $order);
			$order++;
		}

		$division->challenges()->sync($update);

		$division = Division::with('challenges', 'challenges.score_elements')->findOrFail($division_id);

		return View::make('divisions.partial.challenges')
					->with('challenges', $division->challenges)
					->with(compact('division'));

	}

	// Clear all of the challenges from a Division
	public function clearChallenges($division_id) {
		$division = Division::with('challenges')->findOrFail($division_id);
		$count = $division->challenges->count();

		// Remove all challenges by syncing to an empty array
		$division->challenges()->sync([]);

		return redirect()->back()->with('message', "$count Challenges Removed");
	}

	// Copy a list of challenges from one divsion to another
	public function copyChallenges(Request $req, $to_id) {
		$from_id = $req->input('from_id', 0);
		$from = Division::with('challenges')->findOrFail($from_id);
		$to = Division::with('challenges')->findOrFail($to_id);

		foreach($from->challenges as $challenge) {
			$update[$challenge->id] = [ 'display_order' => $challenge->pivot->display_order ];
		}

		$to->challenges()->sync($update);

		return redirect()->back()->with('message', 'Challenges Copied');
	}

	// Clear Scores
	public function clear_scores($id) {
		$count = 0;
		if(Roles::isAdmin()) {
			$count = Score_run::where('division_id', $id)->delete();
		}
		return redirect()->route('divisions.index')->with('message', "$count score runs deleted");
	}

	public function clear_compyear_scores($compyear_id) {
		$count = 0;
		// Clear the cache
		Cache::flush("all_scores_score_list_$compyear_id");
		$compYear = \App\Models\CompYear::with('divisions')->find($compyear_id);
		$divisionList = $compYear->divisions->pluck('id', 'id')->all();
		$count = Score_run::whereIn('division_id', $divisionList)->delete();
		return redirect()->route('compyears.index')->with('message', "$count score runs deleted");
	}

	public function clear_all_scores() {
		$count = 0;
		if(Roles::isAdmin()) {
			$count = DB::table('score_runs')->delete();
		}
		return redirect()->route('divisions.index')->with('message', "$count score runs deleted");
	}

}
