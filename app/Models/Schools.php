<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Schools
 *
 * @property int $school_id
 * @property int $district_id
 * @property string $name
 * @property int $custom
 * @property-read \App\Models\Districts $district
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schools whereCustom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schools whereDistrictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schools whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schools whereSchoolId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schools newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schools newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Schools query()
 */
class Schools extends Model {
	public $connection = 'mysql-wordpress';
	protected $table = 'sdd_school';
	protected $primaryKey = 'school_id';
	protected $guarded = array('school_id');

	public static $rules = array();
	
	public function district() {
		return $this->belongsTo('App\Models\Districts', 'district_id', 'district_id');
	}
}
