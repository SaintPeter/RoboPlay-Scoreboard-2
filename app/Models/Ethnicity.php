<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Ethnicity
 *
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Student[] $students
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ethnicity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ethnicity whereName($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ethnicity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ethnicity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ethnicity query()
 * @property-read int|null $students_count
 */
class Ethnicity extends \Eloquent {
	protected $table = 'ethnicities';
	protected $guarded = array('id');
	public $timestamps = false;

	// Relationships
	public function students() {
		return $this->hasMany('App\Models\Student');
	}
}