<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\CompYear;

class TeacherReminder extends Mailable
{
    use Queueable, SerializesModels;

    // View Variables
	private $template_vars;

	/**
	 * Create a new message instance.
	 *
	 * @param CompYear $comp_year
	 * @param array $general
	 * @param array $teams
	 * @param array $videos
	 */
    public function __construct($subject, $template_vars)
    {
    	$this->template_vars = $template_vars;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.teachers.reminder')
	        ->with($this->template_vars);
    }
}
