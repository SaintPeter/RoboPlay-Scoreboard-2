<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video_review_details extends Model
{
    public $timestamps = false;

    public function category() {
    	return $this->belongsTo(Video_review_categories::class);
    }

}
