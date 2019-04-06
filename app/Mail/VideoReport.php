<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VideoReport extends Mailable
{
    use Queueable, SerializesModels;

    private $template_vars;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($template_vars)
    {
        $this->template_vars = $template_vars;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    	$this->subject = "[RoboPlay Scoreboard] Video '{$this->template_vars['video']->name}' Reported";

        return $this->markdown('emails.admin.video_report')
	        ->with($this->template_vars);
    }
}
