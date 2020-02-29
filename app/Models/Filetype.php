<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Filetype
 *
 * @property int $id
 * @property string $ext
 * @property string $type
 * @property string $name
 * @property string $language
 * @property string $viewer
 * @property string $icon
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Files[] $files
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filetype whereExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filetype whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filetype whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filetype whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filetype whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filetype whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filetype whereViewer($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filetype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filetype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Filetype query()
 * @property-read int|null $files_count
 */
class Filetype extends \Eloquent {
	protected $guarded = ['id'];
	protected $table = 'filetype';
	public $timestamps = false;
	public static $rules = [
	    "ext" => 'required'
    ];

	public function files() {
		return $this->belongsToMany('App\Models\Files');
	}

}