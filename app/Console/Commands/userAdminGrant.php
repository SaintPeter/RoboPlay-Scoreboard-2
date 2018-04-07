<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Enums\UserTypes;
use Illuminate\Console\Command;

class userAdminGrant extends Command
{
	 /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant Admin permissions to a user';

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
        $user = User::where([ 'email' => $this->argument('email')])->first();
        if($user) {
	        $this->info("Original User Roles: " . join(", ", UserTypes::getAllDescriptions($user->roles)));

	        // Grant Admin Role and save
	        $user->roles |= UserTypes::Admin;
	        $user->save();

	        $this->info("New User Roles: " . join(", ", UserTypes::getAllDescriptions($user->roles)));
        } else {
        	$this->warn("No user found for email '" . $this->argument('email') . "'");
        }
    }
}
