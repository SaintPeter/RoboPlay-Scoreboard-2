<?php

namespace App\Console\Commands;

use App\Enums\UserTypes;
use App\Enums\VideoCheckStatus;
use DB;
use App\Models\User;
use App\Models\Invoices;
use Illuminate\Console\Command;

class OneTimeSetInvoiceUsersToTeacher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onetime:set_invoice_users_to_teacher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets all invoice owners to have the teacher role';

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
	    $invoices = Invoices::all();
	    $userList = $invoices->pluck('user_id', 'user_id')->all();

	    $results = DB::table('users')
		    ->whereIn('id', $userList)
		    ->update([ 'roles' => DB::Raw('roles | ' . UserTypes::Teacher )]);
		$this->info($results);
    }
}
