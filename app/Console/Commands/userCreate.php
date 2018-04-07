<?php

namespace App\Console\Commands;

use DB;
use App\Models\User;
use Illuminate\Console\Command;

class userCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {first : First Name} {last : Last Name} {email : E-mail Address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create User Entry';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$email = $this->argument('email');
	    $name = $this->argument('first') . ' ' . $this->argument('last');

    	if($user = User::where('email', $email)->first()) {
    		$this->warn("A user with an e-mail of '$email' already exists.");
    		return 0;
	    }

		$dbMax = DB::table('users')->where('id', '>=', 1000000)->max('id');
    	$newId = ($dbMax >= 1000000) ? $dbMax + 1 : 1000000;

		$user = User::create([
			'id' => $newId,
			'name' => $name,
			'email' => $email,
			'password' => '',
			'tshirt' => '',
			'roles' => 0,
			'remember_token' => ''
		]);

	    $password = $this->secret("Enter Password");
	    $password2 = $this->secret("Re-enter Password");

	    if($password == $password2 && $password != '') {
		    $user->password = bcrypt($password);
		    $user->save();

		    $this->info("Password Set");
	    } else {
		    if($password == '') {
			    $this->warn("Passwords may not be blank, setting password aborted");
		    } else {
			    $this->warn("Passwords did not match, setting password aborted");
		    }
		    return 1;
	    }
	    $this->info("User '$email', id: '$newId' Created");
    }
}
