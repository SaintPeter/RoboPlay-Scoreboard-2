<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserCreated extends Notification implements ShouldQueue
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
	        ->subject("[RoboPlay Scoreboard] New User Account Created")
            ->line('An account has been created for you at the <a href="https://scoreboard.c-stem.ucdavis.edu">RoboPlay Scoreboard</a>.')
            ->line('Your username is this e-mail address. You must set/reset your password to be able to log in.')
            ->action('Reset Password', url('password/reset', $this->token))
            ->line('We encourage you to reset your password as soon as possible.')
            ->line('If you encounter issues, contact <a href="mailto:webmaster@scoreboard.c-stem.ucdavis.edu?subject=RoboPlay New User Issue">webmaster@scoreboard.c-stem.ucdavis.edu</a>');
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
