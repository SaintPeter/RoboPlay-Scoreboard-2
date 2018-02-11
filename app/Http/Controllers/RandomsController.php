<?php

namespace App\Http\Controllers;

use View;
use Validator;
use Illuminate\Http\Request;

use App\ {
    Models\Challenge,
    Models\Random
};
class RandomsController extends Controller {

	/**
	 * Display a listing of randoms
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$randoms = Random::all();

		return View::make('randoms.index', compact('randoms'));
	}

	/**
	 * Show the form for creating a new random
	 *
	 * @param $challenge_id
	 * @return \Illuminate\Http\Response
	 */
	public function create($challenge_id)
	{
		$challenge = Challenge::with('randoms')->findOrFail($challenge_id);
		$order = $challenge->randoms->max('display_order') + 1;

		return View::make('randoms.create')
				   ->with(compact('challenge_id', 'order'));
	}

	/**
	 * Store a newly created random in storage.
	 *
	 * @param Request $req
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $req)
	{
		$validator = Validator::make($data = $req->except([ '_token' ]), Random::$rules);

		if ($validator->fails())
		{
			return redirect()->route('randoms.create', $req->input('challenge_id'))->withErrors($validator)->withInput();
		}

		Random::create($data);

		return 'true';
	}

	/**
	 * Display the specified random.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$random = Random::findOrFail($id);

		return View::make('randoms.show', compact('random'));
	}

	/**
	 * Show the form for editing the specified random.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		$random = Random::find($id);

		return View::make('randoms.edit', compact('random'));
	}

	/**
	 * Update the specified random in storage.
	 *
	 * @param Request $req
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $req, $id)
	{
		$random = Random::findOrFail($id);

		$validator = Validator::make($data = $req->all(), Random::$rules);

		if ($validator->fails())
		{
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$random->update($data);

		return 'true';
	}

	/**
	 * Remove the specified random from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		Random::destroy($id);

		return redirect()->back();
	}

}
