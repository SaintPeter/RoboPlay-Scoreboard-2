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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rubric newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rubric newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Rubric query()
 */
class Rubric extends \Eloquent
{
	protected $table = 'rubric';
	protected $fillable = [
		'vid_score_type_id',
		'element',
		'element_name',
		'order',
		'zero',
		'one',
		'two',
		'three',
		'four',
		'vid_competition_id'
	];
	public $timestamps = false;


	public function vid_score_type() {
		return $this->belongsTo('App\Models\Vid_score_type', 'vid_score_type_id', 'id');
	}

	public function competition() {
		return $this->belongsTo('App\Models\Vid_competition', 'vid_competition_id', 'id');
	}

}