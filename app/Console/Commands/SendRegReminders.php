<?php

namespace App\Console\Commands;

use App\Enums\VideoCheckStatus;
use App\Http\Controllers\TeacherVideoController;
use App\Mail\TeacherReminder;
use Carbon\Carbon;
use App\Models\CompYear;
use App\Models\Invoices;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Console\Input\InputArgument;

class SendRegReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scoreboard:send_reg_reminders ' .
        '{year=0 : Invoice year to send reminders for} ' .
        '{--force : Force Command to run now}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send registration completion reminders to teachers';

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
    	$comp_year = CompYear::CompYearOrMostRecent($this->argument('year'));
    	$year = $comp_year->year;
    	$force = $this->option('force');

    	// Make sure we're within the date range
	    if($this->option('force') || Carbon::now()->between($comp_year->reminder_start, $comp_year->reminder_end)) {
		    // Load invoices which are not unpaid
		    $invoices = Invoices::with('user', 'school')
			    ->with(['videos' => function (HasMany $q) use ($year) {
				    return $q->where('year', $year);
			    }, 'videos.students'])
			    ->with(['teams' => function (HasMany $q) use ($year) {
				    return $q->where('year', $year);
			    }, 'teams.students', 'teams.students.math_level', 'teams.division'])
			    ->where('year', $year)
			    ->where('paid','<>', 0)
			    ->get();

		    $reminder_count = 0;
		    foreach($invoices as $invoice) {
		    	/*
			    Checks:
		    	General -
		    	    * Registered Teams == Teams Count
		    	    * Entered Videos == Videos Count
		    	Per Team:
		    	    * Team Student Count >= 3
		    	Per Video:
		    	    * Video Student Count >= 3
		    	    * Video has not run audit command
		    	*/

		    	// Hold Messages
			    $general = [];
			    $teams = [];
			    $videos = [];

			    // General
		    	if($invoice->teams->count() < $invoice->team_count) {
		    		$general[] = "You have only registered {$invoice->teams->count()} out of {$invoice->team_count} teams you paid for.";
			    }

			    if($invoice->videos->count() < $invoice->video_count) {
				    $general[] = "You have only registered {$invoice->videos->count()} out of {$invoice->video_count} videos you paid for.";
			    }

			    // Teams
			    foreach($invoice->teams as $team) {
		    		if($team->students->count() < 3) {
		    			$teams[$team->name] = "Expected at least 3 students, found {$team->students->count()}";
				    }
			    }
			    
			    // Videos
			    foreach($invoice->videos as $video) {
		    		if($video->students->count() < 3) {
					    $this->append($videos, $video->name,"Expected at least 3 students, found {$video->students->count()}");
				    }

				    list($status, $fails) = TeacherVideoController::check_video_files($video, true, true);

		    		switch($video->status) {
					    case VideoCheckStatus::Warnings:
						    $this->append($videos, $video->name,"Video has validation warnings - Resolve and re-run validation");
						    break;
					    case VideoCheckStatus::Fail:
						    $this->append($videos, $video->name,"Video failed validation - Resolve and re-run validation");
						    break;
				    }

				    if(count($fails)) {
		    			foreach($fails as $fail) {
		    				$this->append($videos, $video->name, $fail['message']);
					    }
				    }
			    }

			    // Build Subject Line, see if we need to send
		    	$subject_arr = [];
		    	$do_send = false;
		    	if(count($general)) {
		    		$subject_arr[] =  "Registration Reminder";
		    		$do_send = true;
			    }

			    if(count($teams)) {
				    $subject_arr[] =  "Team Issues";
				    $do_send = true;
			    }

			    if(count($videos)) {
				    $subject_arr[] =  "Video Issues";
				    $do_send = true;
			    }

			    if($do_send) {
				    $subject = "[RoboPlay Scoreboard] " . join(", ", $subject_arr);

				    Mail::to($invoice->user)
					    ->queue(new TeacherReminder($subject, [
					        'comp_year' => $comp_year,
						    'general' => $general,
						    'teams' => $teams,
						    'videos' => $videos
					    ]));
				    $reminder_count++;
			    }
		    }

		    $this->info(Carbon::now()->toRfc850String() . " - {$reminder_count} Reminders Sent");
		    return true;
	    } else {
	    	$this->info(Carbon::now()->toRfc850String() . " - Not in defined time range.  Use --force to force.");
	    	return false;
	    }
    }

    private function append(&$array, $prop, $value) {
	    if(!array_key_exists($prop, $array)) {
		    $videos[$prop] = [];
	    }
	    $array[$prop][] = $value;
    }
}
