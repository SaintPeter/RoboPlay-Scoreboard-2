<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VideoUpdated extends Mailable
{
    use Queueable, SerializesModels;

    private $video;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($video)
    {
        $this->video = $video;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    	$this->subject = "[RoboPlay Scoreboard] Disqualified Video '{$this->video->name}' Updated";

        return $this->markdown('emails.admin.video_updated')
	        ->with(['video' => $this->video]);
    }
}
