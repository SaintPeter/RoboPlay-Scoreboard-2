<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RandomListElement
 *
 * @property int $id
 * @property int $random_list_id
 * @property string $d1
 * @property string $d2
 * @property string $d3
 * @property string $d4
 * @property string $d5
 * @property-read \App\Models\RandomList $vid_division
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomListElement whereD1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomListElement whereD2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomListElement whereD3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomListElement whereD4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomListElement whereD5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomListElement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomListElement whereRandomListId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomListElement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomListElement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RandomListElement query()
 */
class RandomListElement extends \Eloquent {
	protected $fillable = [ 'd1', 'd2', 'd3', 'd4', 'd5', 'random_list_id'];

	public $timestamps = false;

	// Relationships
	public function vid_division()
	{
		return $this->belongsTo('App\Models\RandomList');
	}

}