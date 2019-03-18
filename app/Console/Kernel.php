<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'SyncInvoices'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
	    $schedule->command('scoreboard:db_sync')
		    ->hourlyAt(0)
		    ->appendOutputTo(base_path('logs/invoice_sync.log'));
	    $schedule->command('scoreboard:invoice_sync')
		    ->hourlyAt(5)
		    ->appendOutputTo(base_path('logs/invoice_sync.log'));

	    $schedule->command('scoreboard:send_reg_reminders')
		    ->mondays()
		    ->dailyAt("8:30")
	        ->appendOutputTo(base_path('logs/reminders.log'));
	    $schedule->command('scoreboard:send_reg_reminders')
		    ->thursdays()
		    ->dailyAt("8:30")
		    ->appendOutputTo(base_path('logs/reminders.log'));;
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
