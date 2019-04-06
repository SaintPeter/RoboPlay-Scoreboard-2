<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\VideoAward
 *
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video[] $videos
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoAward whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoAward whereName($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoAward newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoAward newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\VideoAward query()
 */
class VideoAward extends \Eloquent {
	protected $fillable = ['name'];
	public $timestamps = false;

    public function videos() {
	    return $this->belongsToMany('App\Models\Video');
	}

}