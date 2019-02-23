<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video_review_categories extends Model
{
    public $timestamps = false;

    public function details() {
    	return $this->hasMany(Video_review_details::class, 'category_id')->orderBy('order');
    }
}
