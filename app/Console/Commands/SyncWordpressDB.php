<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class SyncWordpressDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scoreboard:sync_db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync with the remote Wordpress DB';

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
	    $process = new Process([base_path('/db_sync/') . 'remote_export_invoice.sh']);
	    $process->setTimeout(120);
	    $process->run();

	    if (!$process->isSuccessful()) {
	    	$exception = new ProcessFailedException($process);
		    $this->error("Process Failed\n" . $exception->getMessage());
		    return 1;
	    }

	    $this->info("Process Succeeded\n" . $process->getOutput());
	    return 0;
    }
}
