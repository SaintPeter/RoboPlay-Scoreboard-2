<?php

namespace App\Models;

use \Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Competition
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $location
 * @property string $address
 * @property \Carbon\Carbon $event_date
 * @property string $freeze_time
 * @property int $frozen
 * @property int $active
 * @property string $color
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CompYear[] $comp_year
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Division[] $divisions
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Competition whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Competition whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Competition whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Competition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Competition whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Competition whereEventDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Competition whereFreezeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Competition whereFrozen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Competition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Competition whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Competition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Competition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Competition extends Model {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'description' => 'required',
		'location' => 'required',
		'address' => 'required',
		'event_date' => 'required',
		'color' => 'required'
	);

	public function getDates()
    {
        return [ 'created_at', 'updated_at', 'event_date' ];
    }



	public function divisions()
	{
		return $this->hasMany('App\Models\Division')->orderBy('display_order', 'asc');
	}

	public function comp_year() {
		return $this->morphToMany('App\Models\CompYear', 'yearable');
	}

	public function getFreezeTimeAttribute($value)
	{
		return Carbon::parse($value)->format('g:i A');
	}

	public function setFreezeTimeAttribute($value)
	{
		$this->attributes['freeze_time'] = Carbon::parse($value)->format('H:i');
	}

	// Checks to see if it is after the date of the competition
	public function isDone()
	{
	    $today = Carbon::now()->setTimezone('America/Los_Angeles')->startOfDay();
	    return $today->gte($this->event_date->endOfDay());
	}

}
