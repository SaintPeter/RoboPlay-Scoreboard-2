<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Validator;
use App\Helpers\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

use App\Models\ {
	User
};

class AdminController extends Controller
{
	public $tshirt_sizes = [
		0 => '- Pick T-shirt Size -',
		'XS' => 'XS - Extra Small',
		'S' => 'S - Small',
		'M' => 'M - Medium',
		'L' => 'L - Large',
		'XL' => 'XL - Extra Large',
		'XXL' => 'XXL - Extra, Extra Large'
	];

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
		$users = User::with('password_resets')->get();

		View::share('title', 'List Users');
		return View::make('admin.list_users')
			->with(compact('users'));
	}

	public function create_user() {
		View::share('title', 'Add User');

		return View::make('admin.user.create')
			->with(['tshirt_sizes' => $this->tshirt_sizes]);
	}

	public function store_user(Request $req) {
		$input = $req->except(['roles']);
		$roles = $req->input('roles');

		$newRole = 0;
		foreach($roles as $role) {
			$newRole |= $role;
		}
		$input['roles'] = $newRole;

		$userErrors = Validator::make($input, User::$rules);

		if ($userErrors->passes()) {
			$dbMax = DB::table('users')->where('id', '>=', 1000000)->max('id');
			$newId = ($dbMax >= 1000000) ? $dbMax + 1 : 1000000;

			$user = new User;
			$user->fill($input);
			$user->id = $newId;

			if($input['send_password'] == false) {
				$user->password = bcrypt($input['password']);
			}
			$user->save();

			if($input['send_password'] == true) {
				$token = Password::getRepository()->create($user);
				$user->notify(new UserCreated($token));
			}
			return redirect('list_users');

		} else {
			return redirect('create_user')->withErrors($userErrors)->withInput($input);
		}
	}

	public function edit_user(Request $req, User $user) {

	}

	public function update_user(Request $req) {

	}

	public function delete_user(Request $req, $user_id) {

	}

}
