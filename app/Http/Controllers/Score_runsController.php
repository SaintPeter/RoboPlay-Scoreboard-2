<?php

namespace App\Http\Controllers;

use View;
use Validator;
use Illuminate\Http\Request;

use App\ {
    Models\Score_run
};
class Score_runsController extends Controller {

	/**
	 * Score_run Repository
	 *
	 * @var Score_run
	 */
	protected $score_run;

	public function __construct(Score_run $score_run)
	{
		$this->score_run = $score_run;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$score_runs = $this->score_run->all();

		return View::make('score_runs.index', compact('score_runs'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		return View::make('score_runs.create');
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
		$validation = Validator::make($input, Score_run::$rules);

		if ($validation->passes())
		{
			$this->score_run->create($input);

			return redirect()->route('score_runs.index');
		}

		return redirect()->route('score_runs.create')
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
		$score_run = $this->score_run->findOrFail($id);

		return View::make('score_runs.show', compact('score_run'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		$score_run = $this->score_run->find($id);

		if (is_null($score_run))
		{
			return redirect()->route('score_runs.index');
		}

		return View::make('score_runs.edit', compact('score_run'));
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
		$validation = Validator::make($input, Score_run::$rules);

		if ($validation->passes())
		{
			$score_run = $this->score_run->find($id);
			$score_run->update($input);

			return redirect()->route('score_runs.show', $id);
		}

		return redirect()->route('score_runs.edit', $id)
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
		$this->score_run->find($id)->delete();

		return redirect()->route('score_runs.index');
	}

}
