<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Math_Level
 *
 * @property int $id
 * @property string $name
 * @property int $parent
 * @property int $level
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Student[] $students
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Math_Level whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Math_Level whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Math_Level whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Math_Level whereParent($value)
 * @mixin \Eloquent
 */
class Math_Level extends \Eloquent {
    protected $table = "math_level";
	protected $fillable = [];
	public $timestamps = false;

	public static function getList() {
        $levels = Math_Level::all();
        $output = [];

        foreach($levels as $level) {
            if($level->parent == 0) {
                if($level->id == 0) {
                    $output[0] = $level->name;
                } else {
                    $output[$level->name] = [] ;
                }
            } else {
                $parent = $levels->find($level->parent);
                $output[$parent->name][$level->id] = $level->name;
            }
        }
        return $output;
	}

	// Relationships
	public function students() {
		return $this->hasMany('App\Models\Student');
	}

}