<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\School
 *
 * @property int $id
 * @property int $district_id
 * @property int $county_id
 * @property string $name
 * @property string $district
 * @property string $county
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\School whereCounty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\School whereCountyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\School whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\School whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\School whereDistrictId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\School whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\School whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\School whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\School newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\School newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\School query()
 */
class School extends \Eloquent {
    protected $guarded = [];


	public static $rules = [
		// 'title' => 'required'
	];

}