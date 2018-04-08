<?php

namespace App\Console\Commands;

use App\Models\Invoices;
use Illuminate\Console\Command;
use App\Http\Controllers\InvoiceReview;

class OneTimeCreateInvoiceUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onetime:create_invoice_users {years* : List of Invoice Years to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check/Create users for all invoices in a given year';

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
        $invoices = Invoices::whereIn('year', $this->argument('years',[]));
        $userList = $invoices->pluck('user_id', 'user_id')->all();

	    $this->info(InvoiceReview::create_invoice_users($userList, true));
    }
}
