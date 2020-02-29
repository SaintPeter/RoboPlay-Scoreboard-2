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
 * @property int $id
 * @property int $team_id
 * @property int $user_id
 * @property int $spirit
 * @property int $teamwork
 * @property int $persevere
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $judge
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeNominations whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeNominations whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeNominations wherePersevere($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeNominations whereSpirit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeNominations whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeNominations whereTeamwork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeNominations whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JudgeNominations whereUserId($value)
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
