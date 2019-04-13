<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\JudgeNominations
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeNominations newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeNominations newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeNominations query()
 * @mixin \Eloquent
 */
class JudgeNominations extends Model
{
    protected $fillable = [ 'team_id','user_id','spirit', 'teamwork', 'persevere'];
    protected $visible = [ 'spirit', 'teamwork', 'persevere'];

    // relationships
	public function judge() {
		return $this->belongsTo(User::class, 'user_id');
	}

}
