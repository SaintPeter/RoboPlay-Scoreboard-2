<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int $roles
 * @property int|null $tshirt
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRoles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereTshirt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereWpId($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video_scores[] $video_scores
 * @property \Illuminate\Support\Carbon|null $last_login
 * @property-read \App\Models\PasswordResets $password_resets
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video[] $reviewed_videos
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastLogin($value)
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email', 'password', 'tshirt', 'roles',
    ];

    // Date Mutators
    protected $dates = [ 'last_login' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static $rules = [
        'name' => 'required|min:6',
	    'email' => 'required|email|unique:users,email',
	    'password' => 'confirmed|min:6'
    ];

    // Relationships
	public function video_scores() {
		return $this->hasMany('App\Models\Video_scores', 'judge_id', 'id');
	}

	public function score_runs() {
		return $this->hasMany('App\Models\Score_run', 'judge_id', 'id');
	}

	public function videos() {
		return $this->hasMany('App\Models\Video', 'teacher_id', 'id');
	}

	public function teams() {
		return $this->hasMany('App\Models\Team', 'teacher_id', 'id');
	}

	public function password_resets() {
		return $this->hasOne('App\Models\PasswordResets', 'email','email');
	}

	public function reviewed_videos() {
		return $this->hasMany(Video::class, 'reviewer_id');
	}

	/**
	 * @return string
	 */
	public function last_login_formatted() {
		if($this->attributes['last_login']) {
			$last_login = $this->last_login->format('M j, Y');
			$days = $this->last_login->diffInDays(Carbon::now());
			return "$last_login ($days days)";
		} else {
			return 'Never';
		}
	}
    
}
