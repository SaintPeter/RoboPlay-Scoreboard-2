<?php

namespace App\Http\Controllers;

use View;
use Validator;
use Illuminate\Http\Request;

use App\ {
    Models\School
};
class SchoolsController extends Controller {

	/**
	 * Display a listing of schools
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$schools = School::all();

		return View::make('schools.index', compact('schools'));
	}

	/**
	 * Show the form for creating a new school
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		return View::make('schools.create');
	}

	/**
	 * Store a newly created school in storage.
	 *
	 * @param Request $req
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $req)
	{
		$validator = Validator::make($data = $req->all(), School::$rules);

		if ($validator->fails())
		{
			return redirect()->back()->withErrors($validator)->withInput();
		}

		School::create($data);

		return redirect()->route('schools.index');
	}

	/**
	 * Display the specified school.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$school = School::findOrFail($id);

		return View::make('schools.show', compact('school'));
	}

	/**
	 * Show the form for editing the specified school.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		$school = School::find($id);

		return View::make('schools.edit', compact('school'));
	}

	/**
	 * Update the specified school in storage.
	 *
	 * @param Request $req
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $req, $id)
	{
		$school = School::findOrFail($id);

		$validator = Validator::make($data = $req->all(), School::$rules);

		if ($validator->fails())
		{
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$school->update($data);

		return redirect()->route('schools.index');
	}

	/**
	 * Remove the specified school from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		School::destroy($id);

		return redirect()->route('schools.index');
	}

}
