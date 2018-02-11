<?php

namespace App\Http\Controllers;

use View;
use Validator;
use Illuminate\Http\Request;

use App\ {
    Models\Student
};
class StudentsController extends Controller {

	/**
	 * Display a listing of students
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$students = Student::all();

		return View::make('students.index', compact('students'));
	}

	/**
	 * Show the form for creating a new student
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		return View::make('students.create');
	}

	/**
	 * Store a newly created student in storage.
	 *
	 * @param Request $req
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $req)
	{
		$validator = Validator::make($data = $req->all(), Student::$rules);

		if ($validator->fails())
		{
			return 'false';
		}

		Student::create($data);

		return 'true';
	}

	/**
	 * Display the specified student.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$student = Student::findOrFail($id);

		return View::make('students.show', compact('student'));
	}

	/**
	 * Show the form for editing the specified student.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		$student = Student::find($id);

		return View::make('students.edit', compact('student'));
	}

	/**
	 * Update the specified student in storage.
	 *
	 * @param Request $req
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $req, $id)
	{
		$student = Student::findOrFail($id);

		$validator = Validator::make($data = $req->all(), Student::$rules);

		if ($validator->fails())
		{
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$student->update($data);

		return 'true';
	}

	/**
	 * Remove the specified student from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		Student::destroy($id);

		return 'true';
	}

}
