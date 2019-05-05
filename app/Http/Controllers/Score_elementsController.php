<?php

namespace App\Http\Controllers;

use View;
use Response;
use Validator;
use Illuminate\Http\Request;

use App\{
	Models\Score_element, Models\Challenge, Rules\validateScoreMap
};

class Score_elementsController extends Controller {

	/**
	 * Score_element Repository
	 *
	 * @var Score_element
	 */
	protected $score_element;
	public $input_types = [
		'noyes' => 'No/Yes',
		'yesno' => 'Yes/No',
		'low_slider' => 'Low->High Slider',
		'high_slider' => 'High->Low Slider',
		'score_slider' => 'Score Slider',
		'timer' => 'Timer'
	];

	public function __construct(Score_element $score_element)
	{
		$this->score_element = $score_element;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$score_elements = $this->score_element->all();

		return View::make('score_elements.index', compact('score_elements'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param $challenge_id
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $req, $challenge_id)
	{
		$challenge = Challenge::with('score_elements')->findOrFail($challenge_id);
		$order = $challenge->score_elements->max('element_number') + 1;

		$has_score_map = 0;
		if($req->old('score_map', 0) && $req->old('has_score_map',0) == 1) {
			$score_map = $req->old('score_map');
			$has_score_map = 1;
		} else {
			$score_map = [
				[ 'i' => 0, 'v' => 0],
				[ 'i' => 10, 'v' => 10 ]
			];
		}

		return View::make('score_elements.create')
				   ->with(compact('challenge_id', 'order', 'score_map', 'has_score_map'))
				   ->with('input_types', $this->input_types);
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $req
     * @return \Illuminate\Http\Response
     */
	public function store(Request $req)
	{
		if($req->input('has_score_map',0 )) {
			$score_map = $req->input('score_map');

			// Sort by 'i' value
			usort($score_map,function($a, $b) {
				return $a['i'] <=> $b['i'];
			});

			// Remove Duplicate 'i' values
			for($i = 0; $i < count($score_map) - 2; $i++) {
				if($score_map[$i]['i'] == $score_map[$i + 1]['i']) {
					array_splice($score_map, $i,1);
				}
			}

			$req->merge(['score_map' => $score_map]);
		} else {
			$req->request->remove('score_map');
		}

		$validation = Validator::make($req->all(), Score_element::$rules);
		if($req->input('has_score_map',0 )) {
			$validation->sometimes('score_map.*.i', 'required|integer', function($input) {
				return $input->has_score_map;
			});
			$validation->sometimes('score_map.*.v', 'required|integer', function($input) {
				return $input->has_score_map;
			});
		}

		if ($validation->passes())
		{
			$this->score_element->create($req->all());
			$this->update_order($this->score_element->challenge_id);

			return "true";
		}


		return redirect()->route('score_elements.create', $req->input('challenge_id'))
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
		$score_element = $this->score_element->findOrFail($id);

		return View::make('score_elements.show', compact('score_element'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $req, $id)
	{
		$score_element = $this->score_element->find($id);

		if (is_null($score_element))
		{
			return redirect()->route('score_elements.index');
		}

		$has_score_map = 0;
		if($req->old('score_map', 0) && $req->old('has_score_map',0) == 1) {
			$score_map = $req->old('score_map');
			$has_score_map = 1;
		} elseif(count($score_element->score_map)) {
			$score_map = $score_element->score_map;
			$has_score_map = 1;
		} else {
			$score_map = [
				[ 'i' => 0, 'v' => 0],
				[ 'i' => 10, 'v' => 10 ]
			];
		}

		return View::make('score_elements.edit', compact('score_element', 'has_score_map','score_map'))
				   ->with('input_types', $this->input_types);
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
		if($req->has_score_map) {
			$score_map = $req->input('score_map');

			// Sort by 'i' value
			usort($score_map, function ($a, $b) {
				return $a['i'] <=> $b['i'];
			});

			// Remove Duplicate 'i' values
			for ($i = 0; $i < count($score_map) - 2; $i++) {
				if ($score_map[$i]['i'] == $score_map[$i + 1]['i']) {
					array_splice($score_map, $i, 1);
				}
			}
			$req->merge(['score_map' => $score_map]);
		} else {
			$req->merge(['score_map' => [] ]);
		}

		$validation = Validator::make($req->all(), Score_element::$rules);
		$validation->sometimes('score_map.*.i', 'required|integer', function($input) {
			return $input->has_score_map;
		});
		$validation->sometimes('score_map.*.v', 'required|integer', function($input) {
			return $input->has_score_map;
		});

		if ($validation->passes())
		{
			$score_element = $this->score_element->find($id);
			$score_element->update($req->all());

			$this->update_order($this->score_element->challenge_id);

			return "true";
		}

		return redirect()->route('score_elements.edit', $id)
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
		$score_element = $this->score_element->find($id);
		$challenge_id = $score_element->challenge_id;
		$score_element->delete();

		return redirect()->route('challenges.show', $challenge_id);
	}

	public function update_order($challenge_id) {
		$elements = Score_element::where('challenge_id', $challenge_id)->orderBy('element_number', 'ASC')->get();

		$index = 1;
		foreach ($elements as $element) {
			$element->element_number = $index;
			$element->save();
			$index++;
		}
	}

}
