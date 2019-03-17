<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Models\CompYear;

class SyncInvoices extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'scoreboard:invoice_sync {year=0 : Invoice Year to Sync}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Refresh Invoices from Wordpress Database';

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
		$year = CompYear::yearOrMostRecent($this->argument('year'));
		$result = App::make('\App\Http\Controllers\InvoiceReview')->invoice_sync($year,false);
		$this->info(date("r") . " - " . $result);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('year', InputArgument::OPTIONAL, 'The invoice year to sync.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
//		array(
//			array('year', null, InputOption::VALUE_OPTIONAL, 'The invoice year to sync.', null),
//		);
	}

}
