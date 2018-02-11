<?php

namespace App\Http\Controllers;

use View;
use Config;
use Validator;
use Illuminate\Http\Request;

use App\ {
    Models\Vid_division,
    Models\Division,
    Models\Competition,
    Models\CompYear,
    Models\Vid_competition
};

class CompYearsController extends Controller {
	/**
	 * Display a listing of compyears
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
	    $invoice_types = Config::get('settings.invoice_types', []);
		$compyears = CompYear::with('competitions', 'divisions',
									'vid_competitions', 'vid_divisions')
								->orderBy('year', 'desc')
								->get();


		View::share('title', 'Manage Competition Years');
		return View::make('compyears.index', compact('compyears','invoice_types'));
	}

	/**
	 * Show the form for creating a new compyear
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
	    $invoice_types = Config::get('settings.invoice_types', []);
		$competition_list = Competition::all()->pluck('name', 'id')->all();
		$vid_competition_list = Vid_competition::all()->pluck('name', 'id')->all();

		return View::make('compyears.create')
		           ->with(compact('competition_list', 'vid_competition_list', 'invoice_types'));
	}

	/**
	 * Store a newly created compyear in storage.
	 *
	 * @param Request $req
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $req)
	{
		$validator = Validator::make($data = $req->except([ '_token','competitions','vid_competitions' ]), CompYear::$rules);

		if ($validator->fails())
		{
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$compyear = CompYear::firstOrCreate($data);

		$competition_list = $req->input('competitions', [ 0 ]);
		$vid_competition_list = $req->input('vid_competitions', [ 0 ]);

		$division_list = [];
		$division_list = Division::whereIn('competition_id', $competition_list)->pluck('id')->all();

		$vid_divison_list = [];
		$vid_divison_list = Vid_division::whereIn('competition_id', $vid_competition_list)->pluck('id')->all();

		$compyear->competitions()->sync($competition_list);
		$compyear->divisions()->sync($division_list);
		$compyear->vid_competitions()->sync($vid_competition_list);
		$compyear->vid_divisions()->sync($vid_divison_list);

		return redirect()->route('compyears.index');
	}

	/**
	 * Display the specified compyear.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$compyear = CompYear::findOrFail($id);

		return View::make('compyears.show')->with(compact('compyear'));
	}

	/**
	 * Show the form for editing the specified compyear.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
	    $invoice_types = Config::get('settings.invoice_types', []);
		$compyear = CompYear::find($id);

		$competition_list = Competition::all()->pluck('name', 'id')->all();
		$comp_selected = $compyear->competitions()->pluck('yearable_id')->all();
		$vid_competition_list = Vid_competition::all()->pluck('name', 'id')->all();
		$vid_selected = $compyear->vid_competitions()->pluck('yearable_id')->all();

		return View::make('compyears.edit', compact('compyear','competition_list', 'vid_competition_list', 'comp_selected', 'vid_selected', 'invoice_types'));
	}

	/**
	 * Update the specified compyear in storage.
	 *
	 * @param Request $req
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $req, $id)
	{
		$compyear = CompYear::findOrFail($id);

		$validator = Validator::make($data = $req->all(), CompYear::$rules);

		if ($validator->fails())
		{
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$compyear->update($data);

		$competition_list = $req->input('competitions', [ 0 ]);
		$vid_competition_list = $req->input('vid_competitions', [ 0 ]);

		$divison_list = [];
		$division_list = Division::whereIn('competition_id', $competition_list)->pluck('id')->all();

		$vid_divison_list = [];
		$vid_divison_list = Vid_division::whereIn('competition_id', $vid_competition_list)->pluck('id')->all();

		$compyear->competitions()->sync($competition_list);
		$compyear->divisions()->sync($division_list);
		$compyear->vid_competitions()->sync($vid_competition_list);
		$compyear->vid_divisions()->sync($vid_divison_list);

		return redirect()->route('compyears.index');
	}

	/**
	 * Remove the specified compyear from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		CompYear::destroy($id);

		return redirect()->route('compyears.index');
	}

}
