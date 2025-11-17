<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class SetPasswordNotification extends Notification
{
   use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

public function toMail($notifiable)
{
    $url = route('user.set-password', ['user' => $this->user->id]);

    return (new MailMessage)
        ->subject('Set Your Account Password')
        ->greeting('Hello ' . $this->user->name . ',')
        ->line('Your account has been created. Please click the button below to set your password.')
        ->action('Set Password', $url)
        ->line('If you did not expect this email, you can ignore it.');
}

}
