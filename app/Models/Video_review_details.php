<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Video_review_details
 *
 * @property int $id
 * @property int $category_id
 * @property int $order
 * @property string $reason
 * @property string $cfp_section
 * @property int $timestamp_required
 * @property int $resolvable
 * @property-read \App\Models\Video_review_categories $category
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_details newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_details newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_details query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_details whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_details whereCfpSection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_details whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_details whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_details whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_details whereResolvable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_details whereTimestampRequired($value)
 * @mixin \Eloquent
 */
class Video_review_details extends Model
{
    public $timestamps = false;

    public function category() {
    	return $this->belongsTo(Video_review_categories::class);
    }

}
