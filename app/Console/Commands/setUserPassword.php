<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class setUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:password {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the password for a user';

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
	    $user = User::where(['email' => $this->argument('email')])->first();
	    if ($user) {
	        $password = $this->secret("Enter Password");
		    $password2 = $this->secret("Re-enter Password");

		    if($password == $password2) {
			    $user->password = bcrypt($password);
			    $user->save();

			    $this->info("Password Set");
		    } else {
			    $this->warn("Passwords did not match, setting password aborted");
		    }
	    } else {
		    $this->warn("No user found for email '" . $this->argument('email') . "'");
	    }
    }
}
