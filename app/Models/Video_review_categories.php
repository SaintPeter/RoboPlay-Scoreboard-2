<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Video_review_categories
 *
 * @property int $id
 * @property string $name
 * @property int $order
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video_review_details[] $details
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_categories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_categories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_categories query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_categories whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_categories whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Video_review_categories whereOrder($value)
 * @mixin \Eloquent
 * @property-read int|null $details_count
 */
class Video_review_categories extends Model
{
    public $timestamps = false;

    public function details() {
    	return $this->hasMany(Video_review_details::class, 'category_id')->orderBy('order');
    }
}
