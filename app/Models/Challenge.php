<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Challenge
 *
 * @property int $id
 * @property string $internal_name
 * @property string $display_name
 * @property string $rules
 * @property int $points
 * @property int $level
 * @property int $year
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Division[] $divisions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RandomList[] $random_lists
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Random[] $randoms
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Score_element[] $score_elements
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Score_run[] $scores
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Score_run[] $scores_with_trash
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Challenge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Challenge whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Challenge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Challenge whereInternalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Challenge whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Challenge wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Challenge whereRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Challenge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Challenge whereYear($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Challenge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Challenge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Challenge query()
 */
class Challenge extends Model {
	protected $guarded = array();

	public static $rules = array(
		'internal_name' => 'required',
		'display_name' => 'required',
		'rules' => 'required'
	);

	public static $levels = [
		0 => '- Select Level - ',
		1 => 1,
		2 => 2,
		3 => 3,
		4 => 4,
	];

    // Relationships
	public function score_elements()
	{
		return $this->hasMany('App\Models\Score_element')->orderBy('element_number', 'asc');
	}

	public function randoms()
	{
		return $this->hasMany('App\Models\Random')->orderBy('display_order', 'asc');
	}

	public function random_lists()
	{
		return $this->hasMany('App\Models\RandomList')->orderBy('display_order', 'asc');
	}

	public function divisions()
	{
		return $this->belongsToMany('App\Models\Division')->withPivot('display_order');
	}

	public function run_count($team_id)
	{
		return Score_run::where('team_id', $team_id)->where('challenge_id', $this->id)->count();
	}

	public function runs($team_id)
	{
		return Score_run::where('team_id', $team_id)->where('challenge_id', $this->id)->orderBy('run_number', 'asc')->get();
	}

	public function scores()
	{
		return $this->hasMany('App\Models\Score_run')->orderBy('run_number', 'asc');
	}

	public function scores_with_trash()
	{
		return $this->hasMany('App\Models\Score_run')->orderBy('run_number', 'asc')->withTrashed();
	}
}
