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
 */
class Score_element extends Model {
	protected $guarded = array();

	public static $rules = array(
		'display_text' => 'required',
		'element_number' => 'required',
		'base_value' => 'required',
		'multiplier' => 'required',
		'min_entry' => 'required',
		'max_entry' => 'required',
		'type' => 'required',
		'challenge_id' => 'required'
	);

	public function challenge()
	{
		return belongsTo('App\Models\Challenge');
	}

	public function user()
	{
		return hasOne('App\Models\User');
	}
}
