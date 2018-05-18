<?php

namespace App\Http\Controllers;

use View;
use Session;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\ {
    Models\Challenge
};
class ChallengesController extends Controller {

	/**
	 * Challenge Repository
	 *
	 * @var Challenge
	 */
	protected $challenge;

	public function __construct(Challenge $challenge)
	{
		parent::__construct();
		$this->challenge = $challenge;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		// Initialize Challenge Query
		$challenge_query = Challenge::with('score_elements');

		// Selected year, level select set in filters.php -> App::before()
		$year = Session::get('year', false);
		$level_select= Session::get('level_select', false);

		// Filter on Level, if set
		if($level_select) {
			$challenge_query = $challenge_query->where('level', $level_select);
		}

		// Filter on year, if set
		if($year) {
			$challenge_query = $challenge_query->where('year', $year);
		}

		// Get Challenges
		$challenges = $challenge_query->get();

		View::share('title', 'Manage Challenges');
		return View::make('challenges.index', compact('challenges'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//Breadcrumbs::addCrumb('Add Challenge', route('challenges.create'));
		View::share('title', 'Add Challenge');
		return View::make('challenges.create');
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

		$validation = Validator::make($input, Challenge::$rules);

		if ($validation->passes())
		{
			$challenge = $this->challenge->create($input);

			return redirect()->route('challenges.show', [ $challenge->id ]);
		}

		return redirect()->route('challenges.create')
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
		//Breadcrumbs::addCrumb('Show Challenge', route('challenges.show', $id));
		View::share('title', 'Show Challenge');
		$challenge = $this->challenge->with('score_elements', 'randoms', 'random_lists')->findOrFail($id);

		return View::make('challenges.show', compact('challenge'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//Breadcrumbs::addCrumb('Edit Challenge', route('challenges.edit', $id));
		View::share('title', 'Edit Challenge');
		$challenge = $this->challenge->find($id);

		if (is_null($challenge))
		{
			return redirect()->route('challenges.index');
		}

		return View::make('challenges.edit', compact('challenge'));
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
		$validation = Validator::make($input, Challenge::$rules);

		if ($validation->passes())
		{
			$challenge = $this->challenge->find($id);
			$challenge->update($input);

			return redirect()->route('challenges.show', $id);
		}

		return redirect()->route('challenges.edit', $id)
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
		$this->challenge->find($id)->delete();

		return redirect()->route('challenges.index');
	}

	public function clear_cache() {
		\Cache::tags(['challenge_data'])->flush();
		return redirect()->back()->with('message', 'Cache Cleared');
	}

	public function duplicate($id) {
		$challenge = Challenge::with('score_elements','randoms', 'random_lists')->findOrFail($id);

		// Duplicate Challenge Record
		$new_challenge = $challenge->replicate();
		$new_challenge->push();

		// Duplicate Score Elements
		foreach($challenge->score_elements as $element) {
			$new_element = $element->replicate();
			$new_element->challenge_id = $new_challenge->id;
			$new_element->push();
		}

		// Duplicate Random Numbers
		foreach($challenge->randoms as $random) {
			$new_random = $random->replicate();
			$new_random->challenge_id = $new_challenge->id;
			$new_random->push();
		}

		// Duplicate Random Lists
		foreach($challenge->random_lists as $random) {
			$new_random = $random->replicate();
			$new_random->challenge_id = $new_challenge->id;
			$new_random->push();
			foreach($random->elements as $element) {
                $new_element = $element->replicate();
                $new_element->random_list_id = $new_random->id;
                $new_element->push();
			}
		}

		return redirect()->route('challenges.show', [ $new_challenge->id ]);
	}

}
