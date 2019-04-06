<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Video_scores
 *
 * @property int $id
 * @property int $vid_score_type_id
 * @property int $video_id
 * @property int $vid_division_id
 * @property int $user_id
 * @property int $score_group
 * @property int $s1
 * @property int $s2
 * @property int $s3
 * @property int $s4
 * @property int $s5
 * @property int $s6
 * @property int $s7
 * @property int $s8
 * @property int $s9
 * @property int $s10
 * @property int $total
 * @property float $average
 * @property float $norm_avg
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Vid_division $division
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Vid_score_type $type
 * @property-read \App\Models\Video $video
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereAverage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereNormAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereS1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereS10($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereS2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereS3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereS4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereS5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereS6($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereS7($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereS8($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereS9($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereScoreGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereVidDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereVidScoreTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereVideoId($value)
 * @mixin \Eloquent
 * @property int $judge_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores whereJudgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_scores query()
 */
class Video_scores extends \Eloquent {
	//protected $with = [ 'type' ]; // 'video',
	protected $guarded = [ 'id' ];

	public function division() {
		return $this->belongsTo('App\Models\Vid_division', 'vid_division_id', 'id');
	}

	public function video() {
		return $this->hasOne('App\Models\Video', 'id', 'video_id');
	}

	public function type() {
		return $this->hasOne('App\Models\Vid_score_type', 'id', 'vid_score_type_id');
	}

	public function user() {
		return $this->belongsTo('App\Models\User', 'judge_id');
	}

}