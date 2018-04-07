<?php

namespace App\Console\Commands;

use App\Models\Invoices;
use Illuminate\Console\Command;
use App\Http\Controllers\InvoiceReview;

class ForceSyncExistingTeachers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onetime:force_sync_teachers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force users to be created from existing invoices without a notification';

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
    	$userList = Invoices::all()->pluck('user_id', 'user_id')->all();
        $message = InvoiceReview::create_invoice_users($userList, true);
        $this->info($message);
    }
}
