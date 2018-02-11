<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vid_score_type
 *
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property int $group
 * @property-read \App\Models\Vid_competition $competition
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rubric[] $rubric
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video_scores[] $video_scores
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_score_type whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_score_type whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_score_type whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_score_type whereName($value)
 * @mixin \Eloquent
 */
class Vid_score_type extends \Eloquent {
	protected $guarded = [ 'id' ];
	public $timestamps = false;

	public function video_scores() {
		return $this->belongsToMany('App\Models\Video_scores');
	}

	public function rubric() {
		return $this->hasMany('App\Models\Rubric')->orderBy('order');
	}

	public function competition() {
		return $this->hasOne('App\Models\Vid_competition');
	}
}