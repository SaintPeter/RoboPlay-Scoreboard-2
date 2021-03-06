<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Score_element
 *
 * @property int $id
 * @property string $name
 * @property string $display_text
 * @property int $element_number
 * @property int $base_value
 * @property int $multiplier
 * @property int $min_entry
 * @property int $max_entry
 * @property string $type
 * @property int $challenge_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereBaseValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereChallengeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereDisplayText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereElementNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereMaxEntry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereMinEntry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereMultiplier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property float $multiplier2
 * @property int $enforce_limits
 * @property string $score_map
 * @property-read \App\Models\Challenge $challenge
 * @property-read mixed $score_map_raw
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereEnforceLimits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereMultiplier2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_element whereScoreMap($value)
 */
class Score_element extends Model {
	protected $fillable = [
		'name',
		'display_text',
		'element_number',
		'base_value',
		'multiplier',
		'multiplier2',
		'min_entry',
		'max_entry',
		'enforce_limits',
		'type',
		'challenge_id',
		'score_map'
	];

	public static $rules = [
		'display_text' => 'required',
		'element_number' => 'required',
		'base_value' => 'required',
		'multiplier' => 'required',
		'multiplier2' => 'required',
		'min_entry' => 'required',
		'max_entry' => 'required',
		'enforce_limits' => 'required|boolean',
		'type' => 'required',
		'challenge_id' => 'required'
	];

	protected $hidden = ['created_at', 'updated_at'];

	protected $casts = [
		'multiplier' => 'float',
		'multiplier2' => 'float'
	];

	// Accessors and Mutators
	public function setScoreMapAttribute($value) {
		$this->attributes['score_map'] = join(",", array_map(function($subArr) {
			return join(":", $subArr);
		}, $value));
	}

	public function getScoreMapAttribute($value) {
		return array_reduce(preg_split("/,/",$value), function ($acc, $subStr) {
			$temp = preg_split("/:/",$subStr);
			if(count($temp) != 2) return [];
			$acc[] = ['i' => intval($temp[0]), 'v' => intval($temp[1])];
			return $acc;
		},[]);
	}

	public function getScoreMapRawAttribute() {
		return $this->attributes['score_map'];
	}

	// Relations
	public function challenge()
	{
		return $this->belongsTo('App\Models\Challenge');
	}

	public function user()
	{
		return $this->hasOne('App\Models\User');
	}
}
