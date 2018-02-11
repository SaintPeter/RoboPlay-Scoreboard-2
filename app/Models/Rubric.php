<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Rubric
 *
 * @property int $id
 * @property int $vid_score_type_id
 * @property string $element
 * @property string $element_name
 * @property int $order
 * @property string $zero
 * @property string $one
 * @property string $two
 * @property string $three
 * @property string $four
 * @property int $vid_competition_id
 * @property-read \App\Models\Vid_competition $competition
 * @property-read \App\Models\Vid_score_type $vid_score_type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rubric whereElement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rubric whereElementName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rubric whereFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rubric whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rubric whereOne($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rubric whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rubric whereThree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rubric whereTwo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rubric whereVidCompetitionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rubric whereVidScoreTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rubric whereZero($value)
 * @mixin \Eloquent
 */
class Rubric extends \Eloquent {
	protected $table = 'rubric';
	protected $guarded = [ 'id' ];
	public $timestamps = false;
	

	public function vid_score_type()
	{
		return $this->hasOne('App\Models\Vid_score_type');
	}

	public function competition() {
		return $this->hasOne('App\Models\Vid_competition');
	}

}