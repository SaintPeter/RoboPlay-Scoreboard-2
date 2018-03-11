<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Score_run;

class OneTimePopulateAborts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onetime:populateaborts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One Time: Read all of the score_runs records and populate the new aborts column';

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
    	$this->info('Starting . . .');
        $runs = Score_run::all();
        $count = 0;
        foreach($runs as $run) {
        	if($run->scores[1] === 'A') {
        		$run->update(['abort' => true]);
        		$count++;
	        }
        }
	    $this->info("Done.  Modified $count rows of {$runs->count()}");
    }
}
