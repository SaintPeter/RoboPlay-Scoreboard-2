<?php

namespace App\Http\Controllers;

use View;
use Validator;
use Illuminate\Http\Request;

use App\Models\ {
    Vid_competition, User
};
class Vid_competitionsController extends Controller {

	/**
	 * Vid_competition Repository
	 *
	 * @var Vid_competition
	 */
	protected $vid_competition;

	public function __construct(Vid_competition $vid_competition)
	{
		parent::__construct();
		//Breadcrumbs::addCrumb('Video Competitions', 'vid_competitions');
		$this->vid_competition = $vid_competition;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$vid_competitions = $this->vid_competition->orderBy('event_end', 'desc')->get();

		View::share('title', 'Video Competitions');
		return View::make('vid_competitions.index', compact('vid_competitions'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//Breadcrumbs::addCrumb('Add Video Competition', 'create');
		View::share('title', 'Add Video Competition');
		return View::make('vid_competitions.create');
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
		$input['user_id'] = $input['user_id'] ? $input['user_id'] : NULL;

		$validation = Validator::make($input, Vid_competition::$rules);

		if ($validation->passes())
		{
			$this->vid_competition->create($input);

			return redirect()->route('vid_competitions.index');
		}

		return redirect()->route('vid_competitions.create')
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
		//Breadcrumbs::addCrumb('Show Competition', 'show');
		View::share('title', 'Show Competition');
		$vid_competition = $this->vid_competition->findOrFail($id);

		return View::make('vid_competitions.show', compact('vid_competition'));
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
		$vid_competition = $this->vid_competition->with('user')->find($id);

		if (is_null($vid_competition))
		{
			return redirect()->route('vid_competitions.index');
		}

		$vid_competition->event_start->setToStringFormat('Y-m-d');
		$vid_competition->event_end->setToStringFormat('Y-m-d');

		return View::make('vid_competitions.edit', compact('vid_competition'));
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
		$input['user_id'] = $input['user_id'] ? $input['user_id'] : NULL;

		$validation = Validator::make($input, Vid_competition::$rules);

		if ($validation->passes())
		{
			$vid_competition = $this->vid_competition->find($id);
			$vid_competition->update($input);

			return redirect()->route('vid_competitions.index');
		}

		return redirect()->route('vid_competitions.edit', $id)
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
		$this->vid_competition->find($id)->delete();

		return redirect()->route('vid_competitions.index');
	}

	public function user_list($filter) {
		$filter = "%$filter%";
		$users = User::where('name','like',$filter)
			->orWhere('email','like',$filter)
			->get()
			->map(function($user) {
				return $user->only(['id', 'name', 'email']);
			});
		return response()->json($users);
	}

}
