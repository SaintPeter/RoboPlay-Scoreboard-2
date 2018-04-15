<?php

namespace App\Models;

use \Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vid_competition
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon $event_start
 * @property \Carbon\Carbon $event_end
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CompYear[] $comp_year
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vid_division[] $divisions
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_competition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_competition whereEventEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_competition whereEventStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_competition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_competition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vid_competition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Vid_competition extends Model {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'event_start' => 'required|date',
		'event_end' => 'required|date'
	);

	public function getDates()
    {
        return array('created_at', 'updated_at', 'event_start', 'event_end');
    }

	public function divisions() {
		return $this->hasMany('App\Models\Vid_division', 'competition_id', 'id');
	}

	public function comp_year() {
		return $this->morphToMany('App\Models\CompYear', 'yearable');
	}

	public function rubric() {
		return $this->hasMany('App\Models\Rubric','vid_competition_id','id');
	}

	public function scores() {
		return $this->hasManyThrough( 'App\Models\Video_scores','App\Models\Vid_division', 'competition_id','vid_division_id','id', 'id' );
	}

	public function is_active() {
		if(Carbon::now()->between($this->event_start, $this->event_end)) {
			return true;
		}
		return false;
	}
}
