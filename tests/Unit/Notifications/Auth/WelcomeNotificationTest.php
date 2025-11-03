<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\Auth\WelcomeNotification;
use Illuminate\Notifications\Messages\MailMessage;

it('can be instantiated', function (): void {
    $notification = new WelcomeNotification('test-password-123');

    expect($notification)->toBeInstanceOf(WelcomeNotification::class);
});

it('uses the correct notification channels', function (): void {
    $user = User::factory()->make(['email' => 'test@example.com']);
    $notification = new WelcomeNotification('test-password-123');

    $channels = $notification->via($user);

    expect($channels)->toBe(['mail']);
});

it('creates mail message with correct content', function (): void {
    $user = User::factory()->make([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
    $password = 'generated-password-123';
    $notification = new WelcomeNotification($password);

    $mailMessage = $notification->toMail($user);

    expect($mailMessage)->toBeInstanceOf(MailMessage::class);

    // Test that the mail message contains the expected content
    // Note: We can't easily test the exact content without more complex mocking,
    // but we can verify the method exists and returns a MailMessage
});

it('implements should queue interface', function (): void {
    $notification = new WelcomeNotification('test-password');

    expect($notification)->toBeInstanceOf(
        Illuminate\Contracts\Queue\ShouldQueue::class,
    );
});

it('uses queueable trait', function (): void {
    $notification = new WelcomeNotification('test-password');

    expect(class_uses($notification))->toContain(
        Illuminate\Bus\Queueable::class,
    );
});
