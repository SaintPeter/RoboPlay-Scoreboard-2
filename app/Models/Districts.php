<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Districts
 *
 * @property int $district_id
 * @property int $county_id
 * @property string $did
 * @property string $name
 * @property-read \App\Models\Counties $county
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Schools[] $schools
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Districts whereCountyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Districts whereDid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Districts whereDistrictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Districts whereName($value)
 * @mixin \Eloquent
 */
class Districts extends Model {
public $connection = 'mysql-wordpress';
        protected $table = 'sdd_district';
        protected $primaryKey = 'district_id';
        protected $guarded = array('district_id');

        public static $rules = array();

	public function county() {
		return $this->belongsTo('App\Models\Counties','county_id', 'county_id');

	}

	public function schools() {
		return $this->hasMany('App\Models\Schools', 'school_id', 'school_id');
	}
}
