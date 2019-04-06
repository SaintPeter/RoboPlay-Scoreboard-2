<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Video_comment
 *
 * @property int $id
 * @property int $video_id
 * @property int $user_id
 * @property string $comment
 * @property string $resolution
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Video $video
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_comment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_comment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_comment whereResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_comment whereVideoId($value)
 * @mixin \Eloquent
 * @property int $judge_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_comment whereJudgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_comment query()
 */
class Video_comment extends \Eloquent {
	protected $guarded = [ 'id' ];
	protected $table = 'video_comment';

	public function video() {
		return $this->belongsTo('App\Models\Video');
	}

	public function user() {
		return $this->belongsTo('App\Models\User', 'judge_id');
	}
}