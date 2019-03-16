<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Team
 *
 * @property int $id
 * @property string $name
 * @property int $division_id
 * @property int $school_id
 * @property int $teacher_id
 * @property int $year
 * @property int $audit
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Division $division
 * @property-read \App\Models\School $school
 * @property-read \App\Models\Score_run $scores
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Student[] $students
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereAudit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Team whereYear($value)
 * @mixin \Eloquent
 */
class Team extends Model {
	protected $guarded = array();
	//protected $with = [ 'school', 'division', 'division.competition' ];
	protected $fillable = [
		'name', 'division_id', 'school_id', 'teacher_id', 'year', 'audit'
	];

	public static $rules = array(
		'name' => 'required',
		'division_id' => 'required|not_in:0',
		'invoice_id' => 'required'
	);

	public static function boot() {
		parent::boot();

		// Detach Students
		static::deleting(function($team) {
			$team->students()->sync([]);
		});
	}

	// Relationships
	public function division()
	{
		return $this->belongsTo('App\Models\Division');
	}

	public function scores()
	{
		return $this->belongsTo('App\Models\Score_run');
	}

	public function school()
	{
		return $this->belongsTo('App\Models\School');
	}

	public function students() {
		return $this->morphToMany('App\Models\Student', 'studentable');
	}

	public function teacher() {
		return $this->belongsTo('App\Models\User', 'teacher_id');
	}

	public function longname()
	{
		if(isset($this->school)) {
			return $this->name . ' (' . $this->school->name . ')';
		} else {
			return $this->name . ' (Unknown School)';
		}
	}

	public function student_count()
	{
		return $this->students()->count();
	}

	public function student_list()
	{
		$student_list = [];

		if(count($this->students) > 0) {
			foreach($this->students as $student) {
				$student_list[] = $student->fullName();
			}
		} else {
			$student_list = [ 'Warning: No Students' ];
		}

		return $student_list;
	}
}
