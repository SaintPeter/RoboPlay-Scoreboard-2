<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Password;
use Validator;
use App\Helpers\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Notifications\AdminResetPassword;
use App\Notifications\UserCreated;

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
		'XXL' => 'XXL - Extra, Extra Large',
		'3XL' => '3XL - Triple Extra Large'
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

	public function create_users() {
		View::share('title', 'Mass Add Users');

		return View::make('admin.user.mass_create')
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
			return redirect()->route('list_users');

		} else {
			return redirect()->route('create_user')->withErrors($userErrors)->withInput($input);
		}
	}

	public function store_users(Request $req) {

		$newRole = 0;
		foreach($req->input('roles', []) as $role) {
			$newRole |= $role;
		}

		// Break the 'details' into rows for each user
		$userRows = preg_split('/[\r\n]+/', $req->input('details',[]));
		$input['users'] = array_map(function($row) {
			$splitRow = preg_split('/\s+(?=\S*+$)/', trim($row));
			if(count($splitRow) == 2) {
				return [
					'name' => preg_replace('/\s+/',' ',$splitRow[0]),
					'email' => $splitRow[1]
				];
			} else {
				// Force a validation error if it's not the right number of inputs
				return [ 'name' => '', 'email' => ''];
			}
		}, $userRows);

		$userErrors = Validator::make($input, [
			'users.*.name' => 'required|min:6',
			'users.*.email' => 'required|email'
		]);

		if ($userErrors->passes()) {
			$dbMax = DB::table('users')->where('id', '>=', 1000000)->max('id');
			$newId = ($dbMax >= 1000000) ? $dbMax + 1 : 1000000;

			$notifications = 0;
			$creations = 0;
			$existing = 0;
			foreach($input['users'] as $userData) {
				$user = User::firstOrNew(['email' => $userData['email']]);

				// Users without IDs get new ones
				if (!$user->id) {
					$user->id = $newId;
					$user->password = '';
					$user->tshirt = '';
					$user->name = $userData['name'];
					$newId++;
					$creations++;
				} else {
					$existing++;
				}
				// Add whatever permissions were set to their new permissions
				$user->roles |= $newRole;
				$user->save();

				// Send notifications to users with blank passwords
				if (!$user->password) {
					$token = Password::getRepository()->create($user);
					$user->notify(new UserCreated($token));
					$notifications++;
				}
			}
			return redirect()->route('list_users')
				->with([ 'message' => "$creations New Users Added, $existing Existing Users Updated, $notifications Notificaitons Sent" ]);

		} else {
			return redirect()->route('create_users')
				->withErrors($userErrors)
				->withInput($req->all());
		}
	}

	public function edit_user(User $user) {
		View::share('title', 'Edit User');

		return View::make('admin.user.edit')
			->with(['tshirt_sizes' => $this->tshirt_sizes])
			->with(compact('user'));
	}

	public function update_user(User $user) {
		$input = request()->except(['roles']);
		$roles = request()->input('roles');

		$trigger_notification = false;
		if($user->email != request('email')) {
			$trigger_notification = true;
		}

		$newRole = 0;
		foreach($roles as $role) {
			$newRole |= $role;
		}
		$input['roles'] = $newRole;

		$rules = User::$rules;
		$rules['email'] .= "," . $user->id;

		$userErrors = Validator::make($input, $rules);

		if ($userErrors->passes()) {
			$user->update($input);

			$message = [];
			if($trigger_notification == true) {
				$token = Password::getRepository()->create($user);
				$user->notify(new AdminResetPassword($token));
				$message = [ 'message', "Password Reset sent to {$user->name} &lt;{$user->email}&gt;" ];
			}
			return redirect()->route('list_users',[ $user ])->with($message);

		} else {
			return redirect()->route('edit_user', [ $user ])->withErrors($userErrors)->withInput($input);
		}

	}

	public function delete_user(Request $req, $user_id) {

	}

	public function reset_password(User $user) {
		$token = Password::getRepository()->create($user);
		$user->notify(new AdminResetPassword($token));
		return redirect()->route('list_users')
			->with('message', "Password Reset sent to {$user->name} &lt;{$user->email}&gt;");
	}

}
