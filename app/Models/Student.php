<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Student
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property int $nickname
 * @property string $ssid
 * @property string $gender
 * @property int $ethnicity_id
 * @property int $math_level_id
 * @property string|null $tshirt
 * @property int $grade
 * @property string $email
 * @property int $year
 * @property int $teacher_id
 * @property int $school_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Ethnicity $ethnicity
 * @property-read \App\Models\Math_Level $math_level
 * @property-read \App\Models\School $school
 * @property-read \App\Models\User $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereEthnicityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereMathLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereSsid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereTshirt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Student whereYear($value)
 * @mixin \Eloquent
 */
class Student extends \Eloquent {
	protected $table = 'students';
	protected $guarded = array('id');

	public static $rules = [
		'first_name' => 'required',
		'last_name' => 'required',
		'gender' => 'required|not_in:0',
		'ethnicity_id' => 'required|exists:ethnicities,id',
		'math_level_id' => 'required|exists:math_level,id|not_in:0',
		'grade' => 'required|numeric|min:5|max:14',
		'email' => 'sometimes|email'
	];

	// Relationships
	public function ethnicity() {
		return $this->belongsTo('App\Models\Ethnicity');
	}

	public function math_level() {
		return $this->belongsTo('App\Models\Math_Level');
	}

	public function teacher() {
		return $this->belongsTo('App\Models\User', 'teacher_id');
	}

	public function school() {
		return $this->hasOne('App\Models\School');
	}

	public function teams() {
		return $this->morphedByMany('App\Models\Team', 'studentable');
	}

	public function videos() {
		return $this->morphedByMany('App\Models\Video', 'studentable');
	}

	public function fullName() {
		$name = $this->first_name  . " ";
		$name .= (empty($this->middle_name) ? '' : ($this->nickname ? '"' . $this->middle_name . '" ' : $this->middle_name . ' '));
		$name .= $this->last_name;
		return $name;
	}

}