<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Score_run
 *
 * @property int $id
 * @property int $run_number
 * @property string $run_time
 * @property string $scores
 * @property int $total
 * @property int $user_id
 * @property int $team_id
 * @property int $challenge_id
 * @property int $division_id
 * @property string $reason
 * @property \Carbon\Carbon|null $deleted_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Challenge[] $challenges
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Division[] $divisions
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Score_run onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereChallengeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereRunNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereRunTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereScores($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Score_run withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Score_run withoutTrashed()
 * @mixin \Eloquent
 * @property int $judge_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereJudgeId($value)
 * @property int $abort
 * @property-read \App\Models\Challenge $challenge
 * @property-read \App\Models\Division $division
 * @property-read \App\Models\Team $team
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score_run whereAbort($value)
 */
class Score_run extends Model {
	use SoftDeletes;

	protected $guarded = array();

	protected $dates = ['deleted_at'];

	public static $rules = array(
		'run_number' => 'required',
		'run_time' => 'required',
		'scores' => 'required',
		'total' => 'required',
		'user_id' => 'required',
		'team_id' => 'required',
		'challenge_id' => 'required',
		'division_id' => 'required'
	);


	/* Mutators and Assignors
	   ------------------------------ */
	public function getScoresAttribute($value) {
		return unserialize($value);
	}

	public function setScoresAttribute($value) {
		$this->attributes['scores'] = serialize($value);
	}

	// Note:  run_time = RunTime
	public function getRunTimeAttribute($value) {
		if(isset($value)) {
			// Get time from this event return as a string
			return Carbon::parse($value)->format('g:i a');
		} else {
			return "Time Error";
		}
	}

	/* Relationships
	   ------------------------------ */
	public function team()
	{
		return $this->belongsTo('App\Models\Team');
	}

	public function challenge()
	{
		return $this->belongsTo('App\Models\Challenge');
	}

	public function division()
	{
		return $this->belongsTo('App\Models\Division');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User', 'judge_id');
	}

	public function judge()
	{
		return $this->belongsTo('App\Models\User', 'judge_id');
	}
}
