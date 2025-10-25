<?php

declare(strict_types=1);

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class MagicLinkNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $magicLinkUrl)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return new MailMessage()
            ->subject('Sign in to your account')
            ->greeting('Hello!')
            ->line('Click the button below to sign in to your account.')
            ->action('Sign In', $this->magicLinkUrl)
            ->line('This link will expire in 15 minutes for security reasons.')
            ->line(
                'If you did not request this link, please ignore this email.',
            );
    }
}
