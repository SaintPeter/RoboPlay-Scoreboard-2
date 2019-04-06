<?php

namespace App\Mail;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VideoDisqualification extends Mailable
{
    use Queueable, SerializesModels;

    private $video_id = 0;

	/**
	 * Create a new message instance.
	 *
	 * @param $video_id
	 */
    public function __construct($video_id)
    {
        $this->video_id = $video_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    	$video = Video::with(['problems' => function($query) {
    		    return $query->where('resolved', false);
	        }, 'problems.detail'])
		    ->find($this->video_id);

    	$resolvable = $video->problems->contains(function($detail) {
    		return !$detail->resolvable;
	    });

	    $this->subject = "[RoboPlay Scoreboard] Video '{$video->name}' Disqualified";

        return $this->markdown('emails.teachers.video_dq')->with(compact('video','resolvable'));
    }
}
