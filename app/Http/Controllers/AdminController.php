<?php

namespace App\Http\Controllers;

use Auth;
use App\Helpers\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

use App\Models\ {
	User
};

class AdminController extends Controller
{
	// Switch to a different user
	public function switch_user($user_id) {
		if(Roles::isAdmin()) {
			Auth::logout();
			Auth::loginUsingId($user_id);
			return redirect()->to('/')->with('message', 'Logged in');
		}
		return redirect()->to('/')->with('message', 'You do not have permission to do that.');
	}

	// List all Users
	public function list_users() {
		$users = User::all();

		View::share('title', 'List Users');
		return View::make('admin.list_users')->with(compact('users'));
	}
}
