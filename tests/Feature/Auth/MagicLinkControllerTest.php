<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use App\Notifications\Auth\MagicLinkNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it('sends magic link notification for existing user', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    $response = $this->withSession(['_token' => 'test-token'])->post(
        '/login/magic-link',
        [
            '_token' => 'test-token',
            'email' => 'test@example.com',
        ],
    );

    $response->assertRedirect();
    $response->assertSessionHas(
        'message',
        'We\'ve sent a magic link to your email address. Please check your inbox.',
    );

    Notification::assertSentTo($user, MagicLinkNotification::class);
});

it('returns error for non-existent email', function () {
    $response = $this->withSession(['_token' => 'test-token'])->post(
        '/login/magic-link',
        [
            '_token' => 'test-token',
            'email' => 'nonexistent@example.com',
        ],
    );

    $response->assertRedirect();
    $response->assertSessionHasErrors([
        'email' => 'We couldn\'t find an account with that email address.',
    ]);
});

it('authenticates user with valid magic link', function () {
    $adminRole = Role::query()->where('name', 'admin')->first();
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'name' => 'Admin User',
    ]);
    $user->roles()->attach($adminRole);

    $signedUrl = URL::temporarySignedRoute(
        'login.magic-link.verify',
        now()->addMinutes(15),
        ['user' => $user->id],
    );

    $response = $this->get($signedUrl);

    $response->assertRedirect('/dashboard');
    $response->assertSessionHas('greeting', 'Hello, Admin User');
    $this->assertAuthenticatedAs($user);
});

it('returns 403 for expired magic link', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    $expiredSignedUrl = URL::temporarySignedRoute(
        'login.magic-link.verify',
        now()->subHours(1), // Expired
        ['user' => $user->id],
    );

    $response = $this->get($expiredSignedUrl);

    $response->assertForbidden();
    $this->assertGuest();
});

it('validates email field for magic link request', function () {
    $response = $this->withSession(['_token' => 'test-token'])->post(
        '/login/magic-link',
        [
            '_token' => 'test-token',
        ],
    );

    $response->assertRedirect();
    $response->assertSessionHasErrors(['email']);
});
