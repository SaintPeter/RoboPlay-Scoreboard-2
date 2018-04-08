<?php

namespace App\Http\Controllers;

use View;
use Validator;
use Illuminate\Http\Request;

use App\ {
    Models\Vid_division,
    Models\Vid_competition
};
class Vid_divisionsController extends Controller {

	/**
	 * Vid_division Repository
	 *
	 * @var Vid_division
	 */
	protected $vid_division;

	public function __construct(Vid_division $vid_division)
	{
		parent::__construct();
		//Breadcrumbs::addCrumb('Video Divisions', 'vid_division');
		$this->vid_division = $vid_division;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$vid_divisions = $this->vid_division->all();

		View::share('title', 'Video Divisions');
		return View::make('vid_divisions.index', compact('vid_divisions'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//Breadcrumbs::addCrumb('Add Division', 'create');
		View::share('title', 'Add Division');
		$competitions = Vid_competition::pluck('name','id')->all();

		return View::make('vid_divisions.create')
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
		$validation = Validator::make($input, Vid_division::$rules);

		if ($validation->passes())
		{
			$this->vid_division->create($input);

			return redirect()->route('vid_divisions.index');
		}

		return redirect()->route('vid_divisions.create')
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
		//Breadcrumbs::addCrumb('Show Division', 'create');
		View::share('title', 'Show Division');
		$vid_division = $this->vid_division->findOrFail($id);

		return View::make('vid_divisions.index', compact('vid_division'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//Breadcrumbs::addCrumb('Edit Division', 'create');
		View::share('title', 'Edit Division');
		$vid_division = $this->vid_division->find($id);
		$competitions = Vid_competition::pluck('name','id')->all();

		if (is_null($vid_division))
		{
			return redirect()->route('vid_divisions.index');
		}

		return View::make('vid_divisions.edit', compact('vid_division'))
				   ->with('competitions',$competitions);
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
		$validation = Validator::make($input, Vid_division::$rules);

		if ($validation->passes())
		{
			$vid_division = $this->vid_division->find($id);
			$vid_division->update($input);

			return redirect()->route('vid_divisions.show', $id);
		}

		return redirect()->route('vid_divisions.edit', $id)
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
		$this->vid_division->find($id)->delete();

		return redirect()->route('vid_divisions.index');
	}

}
