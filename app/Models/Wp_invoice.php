<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Wp_invoice
 *
 * @property int $invoice_no
 * @property int $user_id
 * @property string $division
 * @property int $division_id
 * @property int $vid_division_id
 * @property int $school_id
 * @property int $team_count
 * @property int $video_count
 * @property int $pmath_count
 * @property int $amath_count
 * @property int $T_XS
 * @property int $T_S
 * @property int $T_M
 * @property int $T_L
 * @property int $T_XL
 * @property float $total
 * @property int $paid
 * @property string $Date
 * @property-read \App\Models\Division $challenge_division
 * @property-read mixed $date
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Schools $school
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 * @property-read \App\Models\Wp_user $wp_user
 * @property-read \App\Models\Vid_division $vid_division
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video[] $videos
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereAmathCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereDivision($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereInvoiceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice wherePmathCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereTL($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereTM($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereTS($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereTXL($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereTXS($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereTeamCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereVidDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice whereVideoCount($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_invoice query()
 * @property-read int|null $teams_count
 * @property-read int|null $videos_count
 */
class Wp_invoice extends Model {
	public $connection = 'mysql-wordpress';
	protected $table = 'CSTEM_Day_Invoice';
	protected $primaryKey = 'invoice_no';
	protected $guarded = array('invoice_no');
	public $timestamps = false;

	public static $rules = array();

	public function wp_user() {
		return $this->belongsTo('App\Models\Wp_user', 'user_id', 'ID');
	}

	public function user() {
		return $this->belongsTo('App\Models\User', 'user_id', 'id' );
	}

	public function videos() {
		return $this->hasMany('App\Models\Video', 'school_id', 'school_id');
	}

	public function teams() {
		return $this->hasMany('App\Models\Team', 'school_id', 'school_id');
	}

	public function school() {
		return $this->belongsTo('App\Models\Schools', 'school_id', 'school_id');
	}

	public function challenge_division() {
		return $this->hasOne('App\Models\Division', 'id', 'division_id');
	}

	public function vid_division() {
		return $this->belongsTo('App\Models\Vid_division', 'vid_division_id', 'id');
	}

	public function getDateAttribute($value) {
		if(isset($value)) {
			// Get time from this event, change it to local time, return as a string
			$dt = new Carbon($value, new DateTimeZone("UTC"));
			$dt->setTimeZone("PST");
			return $dt->format('n/j/Y');
		} else {
			return "Time Error";
		}
	}
}
