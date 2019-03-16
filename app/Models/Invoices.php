<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Invoices
 *
 * @property int $id
 * @property int $remote_id
 * @property int $user_id
 * @property int $wp_school_id
 * @property string $notes
 * @property int $team_count
 * @property int $video_count
 * @property int $math_count
 * @property int $paid
 * @property int $year
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\School $school
 * @property-read \App\Models\Wp_user $wp_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video[] $videos
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoices whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoices whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoices whereMathCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoices whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoices wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoices whereRemoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoices whereTeamCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoices whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoices whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoices whereVideoCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoices whereWpSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoices whereYear($value)
 * @mixin \Eloquent
 * @property int $school_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invoices whereSchoolId($value)
 */
class Invoices extends \Eloquent {
	protected $fillable = ['remote_id', 'paid', 'notes', 'user_id', 'year'];

	// Relationships
	public function wp_user() {
		return $this->belongsTo('App\Models\Wp_user', 'user_id', 'ID');
	}

	public function user() {
		return $this->belongsTo('App\Models\User', 'user_id', 'id' );
	}

	public function teacher() {
		return $this->belongsTo('App\Models\User', 'user_id', 'id' );
	}

	public function school() {
	    return $this->belongsTo('App\Models\School');
	}

	public function videos() {
		return $this->hasMany('App\Models\Video', 'teacher_id', 'user_id');
	}

	public function teams() {
		return $this->hasMany('App\Models\Team', 'teacher_id', 'user_id');
	}

}