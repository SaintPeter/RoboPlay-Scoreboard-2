<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Wp_user
 *
 * @property int $ID
 * @property string $user_login
 * @property string $user_pass
 * @property string $user_nicename
 * @property string $user_email
 * @property string $user_url
 * @property string $user_registered
 * @property string $user_activation_key
 * @property int $user_status
 * @property string $display_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Usermeta[] $usermeta
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_user whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_user whereID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_user whereUserActivationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_user whereUserEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_user whereUserLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_user whereUserNicename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_user whereUserPass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_user whereUserRegistered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_user whereUserStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_user whereUserUrl($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_user newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_user newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Wp_user query()
 * @property-read int|null $usermeta_count
 */
class Wp_user extends Model {
	public $connection = 'mysql-wordpress';
	protected $table = 'users';
	protected $primaryKey = 'ID';
	protected $guarded = array('ID');
	public $timestamps = false;

	public static $rules = array();

	public $metadata = [];

	public function usermeta() {
		return $this->hasMany('App\Models\Usermeta', 'user_id');
	}

	public function getName() {
		return ucwords($this->getMeta('first_name') . ' ' . $this->getMeta('last_name'));
	}

	public function getNameProper() {
		return ucwords($this->getMeta('last_name') . ", " . $this->getMeta('first_name')) ;
	}

	public function getSchool() {
		if($this->getMeta('wp_school_id', false)) {
			return Schools::find($this->getMeta('wp_school_id'))->name;
		} else {
			return "No School Set";
		}
	}

	public function getMeta($key, $default = '') {
		if(empty($this->metadata)) {
			$this->metadata = $this->usermeta->pluck('meta_value', 'meta_key')->all();
		}
		if(array_key_exists($key, $this->metadata)) {
			return $this->metadata[$key];
		} else {
			return $default;
		}

	}

}