<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\Auth\MagicLinkNotification;
use Illuminate\Notifications\Messages\MailMessage;

it('can be instantiated', function (): void {
    $url = 'https://example.com/magic-link?signature=abc123';
    $notification = new MagicLinkNotification($url);

    expect($notification)->toBeInstanceOf(MagicLinkNotification::class);
});

it('uses the correct notification channels', function (): void {
    $url = 'https://example.com/magic-link?signature=abc123';
    $notification = new MagicLinkNotification($url);
    $user = User::factory()->make();

    $channels = $notification->via($user);

    expect($channels)->toBe(['mail']);
});

it('creates mail message with correct content', function (): void {
    $url = 'https://example.com/magic-link?signature=abc123';
    $notification = new MagicLinkNotification($url);
    $user = User::factory()->make();

    $mailMessage = $notification->toMail($user);

    expect($mailMessage)->toBeInstanceOf(MailMessage::class);
    expect($mailMessage->subject)->toBe('Sign in to your account');
    expect($mailMessage->greeting)->toBe('Hello!');
    expect($mailMessage->introLines)->toContain(
        'Click the button below to sign in to your account.',
    );
    expect($mailMessage->actionText)->toBe('Sign In');
    expect($mailMessage->actionUrl)->toBe($url);
    expect($mailMessage->outroLines)->toContain(
        'This link will expire in 15 minutes for security reasons.',
    );
    expect($mailMessage->outroLines)->toContain(
        'If you did not request this link, please ignore this email.',
    );
});

it('implements should queue interface', function (): void {
    $url = 'https://example.com/magic-link?signature=abc123';
    $notification = new MagicLinkNotification($url);

    expect($notification)->toBeInstanceOf(
        Illuminate\Contracts\Queue\ShouldQueue::class,
    );
});

it('uses queueable trait', function (): void {
    $reflection = new ReflectionClass(MagicLinkNotification::class);
    $traits = $reflection->getTraitNames();

    expect($traits)->toContain(Illuminate\Bus\Queueable::class);
});
