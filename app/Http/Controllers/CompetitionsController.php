<?php

namespace App\Http\Controllers;

use DB;
use View;
use Response;
use Validator;
use Illuminate\Http\Request;

use App\ {
    Models\Competition
};

class CompetitionsController extends Controller {

	/**
	 * Competition Repository
	 *
	 * @var Competition
	 */
	protected $competition;

	public function __construct(Competition $competition)
	{
		parent::__construct();
		$this->competition = $competition;
		//Breadcrumbs::addCrumb('Manage Competitions', 'competitions');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$competitions = Competition::orderBy('event_date', 'desc')->get();

		View::share('title', 'Manage Competitions');
		return View::make('competitions.index', compact('competitions'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		View::share('title', 'Add Competition');
		//Breadcrumbs::addCrumb('Add Competition', 'create');
		return View::make('competitions.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $req
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $req)
	{
		$input = array_except($req->all(), [ 'hour', 'minute', 'meridian' ]);
		$validation = Validator::make($input, Competition::$rules);

		if ($validation->passes())
		{
			$this->competition->create($input);

			return redirect()->route('competitions.index');
		}

		return redirect()->route('competitions.create')
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
		$competition = $this->competition->findOrFail($id);

		View::share('title', 'Show Competition');
		return View::make('competitions.show', compact('competition'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//Breadcrumbs::addCrumb('Edit Competition', 'edit');
		View::share('title', 'Edit Competition');
		$competition = $this->competition->find($id);

		if (is_null($competition))
		{
			return redirect()->route('competitions.index');
		}

		return View::make('competitions.edit', compact('competition'));
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
		$input = array_except($req->all(), [ '_method', 'hour', 'minute', 'meridian' ]);
		$validation = Validator::make($input, Competition::$rules);

		if ($validation->passes())
		{
			$competition = $this->competition->find($id);
			$competition->update($input);

			return redirect()->route('competitions.index');
		}

		return redirect()->route('competitions.edit', $id)
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
		$this->competition->find($id)->delete();

		return redirect()->route('competitions.index');
	}

	public function toggle_frozen($competition_id)
	{
		$comp = Competition::find($competition_id);
		$comp->update(['frozen' => !$comp->frozen ]);

		return redirect()->route('competitions.index');
	}

	public function toggle_active($competition_id)
	{
		$comp = Competition::find($competition_id);
		$comp->update(['active' => !$comp->active ]);

		return redirect()->route('competitions.index');
	}

	public function freeze_all()
	{
		$count = DB::table('competitions')->update( [ 'frozen' => true ]);
		return redirect()->route('competitions.index')->with('message', "$count Competitions Frozen");
	}

	public function unfreeze_all()
	{
		$count = DB::table('competitions')->update( [ 'frozen' => false ]);
		return redirect()->route('competitions.index')->with('message', "$count Competitions Unfrozen");
	}
}
