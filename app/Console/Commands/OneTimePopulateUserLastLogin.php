<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class OneTimePopulateUserLastLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onetime:populate_user_last_login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the user last_login field with best guesses';

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
	    $updated_at_desc = function($q) { return $q->orderBy('updated_at', 'desc'); };
	    $created_at_desc = function($q) { return $q->orderBy('created_at', 'desc'); };

        $users = User::with([
        	'score_runs' => $created_at_desc,
	        'video_scores' => $created_at_desc,
	        'videos' => $updated_at_desc,
	        'teams' => $updated_at_desc,
	        ])
	        ->whereNull('last_login')
	        ->get();

        foreach($users as $user) {
            $last_vid_date = $user->video_scores->count() ? $user->video_scores->first()->updated_at : null;
            $last_score_date = $user->score_runs->count() ? $user->score_runs->first()->updated_at : null;
            $last_team_date =$user->teams->count() ? $user->teams->first()->updated_at : null;
	        $last_video_date =$user->videos->count() ? $user->videos->first()->updated_at : null;
	        
	        $new_date = max($last_vid_date, $last_score_date, $last_team_date, $last_video_date);
	        //$this->info(join(",",[$last_vid_date, $last_score_date, $last_team_date, $last_video_date]));

        	if($new_date) {
        	    $user->last_login = $new_date;
        	    $user->save();
        	    $this->info($user->name . ": " . $new_date);
	        }
        }

        return 1;
    }
}
