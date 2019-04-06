<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Video_review_problems
 *
 * @property int $id
 * @property int $video_id
 * @property int $reviewer_id
 * @property int $order
 * @property int $video_review_details_id
 * @property int|null $timestamp
 * @property string $comment
 * @property int $resolved
 * @property int|null $resolver_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Video_review_details $detail
 * @property-read mixed $formatted_timestamp
 * @property-read \App\Models\User|null $resolver
 * @property-read \App\Models\User $reviewer
 * @property-read \App\Models\Video $video
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_problems newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_problems newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_problems query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_problems whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_problems whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_problems whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_problems whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_problems whereResolved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_problems whereResolverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_problems whereReviewerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_problems whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_problems whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_problems whereVideoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_problems whereVideoReviewDetailsId($value)
 * @mixin \Eloquent
 */
class Video_review_problems extends Model
{
	public $fillable = [
		'video_id',
		'reviewer_id',
		'order',
		'video_review_details_id',
		'timestamp',
		'comment',
		'resolved',
		'resolver_id',
	];

	public function getFormattedTimestampAttribute() {
		if($this->timestamp > -1) {
			return sprintf("%0d:%02d", $this->timestamp / 60, $this->timestamp % 60);
		} else {
			return "0:00";
		}
	}
	
	public function video() {
		return $this->belongsTo(Video::class);
	}

	public function reviewer() {
		return $this->belongsTo(User::class,'reviewer_id');
	}

	public function resolver() {
		return $this->belongsTo(User::class,'resolver_id');
	}

	public function detail() {
		return $this->belongsTo(Video_review_details::class, 'video_review_details_id');
	}

}
