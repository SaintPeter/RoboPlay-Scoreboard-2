<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AdminResetPassword extends Notification
{
    use Queueable;
	private $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
	        ->subject("[RoboPlay Scoreboard] Password Reset by Admin")
	        ->line('An Admin has reset your password for you at <a href="https://scoreboard.c-stem.ucdavis.edu">RoboPlay Scoreboard</a>.')
	        ->line('Your username is this e-mail address.')
	        ->action('Reset Password', url('password/reset', $this->token))
	        ->line('If you have any concerns, please contact <a href="mailto:webmaster@scoreboard.c-stem.ucdavis.edu?subject=RoboPlay Password Reset Issue">webmaster@scoreboard.c-stem.ucdavis.edu</a>');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
