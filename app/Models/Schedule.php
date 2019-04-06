<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * App\Models\Schedule
 *
 * @property int $id
 * @property string $start
 * @property string $end
 * @property string $display
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule whereStart($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schedule query()
 */
class Schedule extends \Eloquent {
	protected $fillable = [ 'start', 'end', 'display'];
	public $timestamps = false;
	public $table = 'schedule';

	public static $rules = [
	    'start' => 'required|date_format:h:i a',
	    'end' => 'required|date_format:h:i a',
	    'display' => 'required'
	];

	public function getStartAttribute($value) {
	    return Carbon::createFromFormat('H:i:s', $value);
	}

	public function setStartAttribute($value) {
	    $this->attributes['start'] = Carbon\Carbon::parse($value)->toTimeString();
	}

	public function getEndAttribute($value) {
	    return Carbon::createFromFormat('H:i:s', $value);
	}

	public function setEndAttribute($value) {
	    $this->attributes['end'] = Carbon\Carbon::parse($value)->toTimeString();
	}

}
