<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use App\Notifications\Auth\MagicLinkNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed();
});

it('sends magic link notification for existing user', function (): void {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    $response = $this->withSession(['_token' => 'test-token'])->post(
        '/auth/magic-link',
        [
            '_token' => 'test-token',
            'email' => 'test@example.com',
        ],
    );

    $response->assertRedirect();
    $response->assertSessionHas(
        'message',
        "We've sent a magic link to your email address. Please check your inbox.",
    );

    Notification::assertSentTo($user, MagicLinkNotification::class);
});

it('returns error for non-existent email', function (): void {
    $response = $this->withSession(['_token' => 'test-token'])->post(
        '/auth/magic-link',
        [
            '_token' => 'test-token',
            'email' => 'nonexistent@example.com',
        ],
    );

    $response->assertRedirect();
    $response->assertSessionHasErrors([
        'email' => "We couldn't find an account with that email address.",
    ]);
});

it('authenticates user with valid magic link', function (): void {
    $adminRole = Role::query()->where('name', 'admin')->first();
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'name' => 'Admin User',
    ]);
    $user->roles()->attach($adminRole);

    $signedUrl = URL::temporarySignedRoute(
        'auth.magic-link.show',
        now()->addMinutes(15),
        ['user' => $user->id],
    );

    $response = $this->get($signedUrl);

    $response->assertRedirect('/admin/dashboard');
    $response->assertSessionHas('greeting', 'Hello, Admin User');
    $this->assertAuthenticatedAs($user);
});

it('returns 403 for expired magic link', function (): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    $expiredSignedUrl = URL::temporarySignedRoute(
        'auth.magic-link.show',
        now()->subHours(1), // Expired
        ['user' => $user->id],
    );

    $response = $this->get($expiredSignedUrl);

    $response->assertForbidden();
    $this->assertGuest();
});

it('validates email field for magic link request', function (): void {
    $response = $this->withSession(['_token' => 'test-token'])->post(
        '/auth/magic-link',
        [
            '_token' => 'test-token',
        ],
    );

    $response->assertRedirect();
    $response->assertSessionHasErrors(['email']);
});

it('returns 403 for invalid signature magic link', function (): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    // Create an invalid signed URL by accessing the route without proper signature
    $response = $this->get('/auth/magic-link/'.$user->id);

    $response->assertForbidden();
    $this->assertGuest();
});

it('executes abort when signature is invalid', function (): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    // Use a manually crafted URL with invalid signature to trigger the abort
    $invalidUrl =
        route('auth.magic-link.show', ['user' => $user->id]).
        '?signature=invalid';

    $response = $this->get($invalidUrl);

    $response->assertStatus(403);
    $this->assertGuest();
});

it(
    'greets user with welcome when they have no admin or employee role',
    function (): void {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'name' => 'Regular User',
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'auth.magic-link.show',
            now()->addMinutes(15),
            ['user' => $user->id],
        );
        $response = $this->get($signedUrl);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('greeting', 'Welcome, Regular User');
        $this->assertAuthenticatedAs($user);
    },
);

it('greets employee user with Hi after magic link login', function (): void {
    $employeeRole = Role::query()->where('name', 'employee')->first();
    $user = User::factory()->create([
        'email' => 'employee@example.com',
        'name' => 'Employee User',
    ]);
    $user->roles()->attach($employeeRole);

    $signedUrl = URL::temporarySignedRoute(
        'auth.magic-link.show',
        now()->addMinutes(15),
        ['user' => $user->id],
    );

    $response = $this->get($signedUrl);

    $response->assertRedirect('/dashboard');
    $response->assertSessionHas('greeting', 'Hi, Employee User');
    $this->assertAuthenticatedAs($user);
});
