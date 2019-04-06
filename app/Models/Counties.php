<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Counties
 *
 * @property int $county_id
 * @property string $name
 * @property string $state
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Districts[] $districts
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Counties whereCountyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Counties whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Counties whereState($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Counties newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Counties newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Counties query()
 */
class Counties extends Model {
	public $connection = 'mysql-wordpress';
        protected $table = 'sdd_county';
        protected $primaryKey = 'county_id';
        protected $guarded = array('county_id');

        public static $rules = array();

	public function districts() {
		return $this->hasMany('App\Models\Districts', 'district_id');
	}
}
