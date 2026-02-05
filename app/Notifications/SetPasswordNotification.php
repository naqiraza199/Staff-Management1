<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SetPasswordNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $token;

    public function __construct($user)
    {
        $this->user = $user;
        // Generate a unique token for this password set request
        $this->token = hash('sha256', $user->id . now()->timestamp . Str::random(40));
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Store the token in the user's record
        $this->user->set_password_token = $this->token;
        $this->user->set_password_sent_at = now();
        $this->user->save();

        // Create a signed URL that expires in 24 hours
        $signedUrl = URL::temporarySignedRoute(
            'user.set-password',
            now()->addHours(24),
            ['user' => $this->user->id, 'token' => $this->token]
        );

        return (new MailMessage)
            ->subject('Set Your Account Password')
            ->greeting('Hello ' . $this->user->name . ',')
            ->line('Your account has been created. Please click the button below to set your password.')
            ->action('Set Password', $signedUrl)
            ->line('This link will expire in 24 hours for security purposes.')
            ->line('If you did not expect this email, you can ignore it.');
    }
}
