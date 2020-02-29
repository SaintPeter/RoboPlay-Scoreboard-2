<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\JudgeAwards
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeAwards newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeAwards newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeAwards query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $col
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeAwards whereCol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeAwards whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeAwards whereName($value)
 */
class JudgeAwards extends Model
{
    public $timestamps = false;



}
