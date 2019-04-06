<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Video;

class VideoReviewNotification extends Mailable
{
    use Queueable, SerializesModels;

    private $video_id;

    /**
     * Create a new message instance.
     *
     * @return void
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
	    }, 'problems.detail', 'problems.reviewer'])
		    ->find($this->video_id);

	    $resolvable = $video->problems->contains(function($detail) {
		    return !$detail->resolvable;
	    });

	    $this->subject = "[RoboPlay Scoreboard] Video '{$video->name}' Disqualified";

        return $this->markdown('emails.admin.video_reviewed')
	        ->with(compact('video','resolvable'));
    }
}
