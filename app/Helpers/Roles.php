<?php

namespace App\Helpers;

use Auth;
use App\Enums\UserTypes;

class Roles {

	public static function isAdmin()
	{
		return Roles::is(UserTypes::Admin,UserTypes::SuperAdmin);
	}

	public static function isJudge()
	{
		return Roles::is(UserTypes::Judge,UserTypes::Admin,UserTypes::SuperAdmin);
	}

	public static function isTeacher()
	{
		return Roles::is(UserTypes::Teacher,UserTypes::Admin,UserTypes::SuperAdmin);
	}

	public static function isVideoReviewer()
	{
		return Roles::is(UserTypes::VideoReviewer,UserTypes::Admin,UserTypes::SuperAdmin);
	}

	public static function is()
	{
		// Check for args
		if(func_num_args() == 0) {
			return false;
		}

		// Must be logged in
		if(Auth::guest()) {
			return false;
		}

		$args = func_get_args();
		$roles = Auth::user()->roles;

		// Check to see if the user has the role
		foreach($args as $roleCheck)
		{
			if($roles & $roleCheck) {
                return true;
            }
		}

		// If not found, return false
		return false;
	}
}