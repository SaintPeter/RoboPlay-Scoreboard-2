<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video_review_problems extends Model
{
	public $guarded = ['id'];

	public function video() {
		return $this->belongsTo(Videos::class);
	}

	public function reviewer() {
		return $this->belongsTo(User::class,'reviewer_id');
	}

	public function resolver() {
		return $this->belongsTo(User::class,'resolver_id');
	}

}
