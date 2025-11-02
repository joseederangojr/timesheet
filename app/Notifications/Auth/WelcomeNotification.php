<?php

declare(strict_types=1);

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $password)
    {
        //
    }

    /**
     * @return array<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return new MailMessage()
            ->subject('Welcome to Timesheet - Your Account Details')
            ->greeting('Welcome to Timesheet!')
            ->line('Your account has been created successfully.')
            ->line('Here are your login credentials:')
            ->line('**Email:** '.$notifiable->email)
            ->line('**Password:** '.$this->password)
            ->action('Sign In', route('login'))
            ->line(
                'Please change your password after your first login for security.',
            )
            ->line(
                'If you have any questions, please contact your administrator.',
            );
    }
}
