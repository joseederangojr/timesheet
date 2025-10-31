<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use App\Notifications\Auth\MagicLinkNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed();
});

it('displays the login page with magic link as default', function (): void {
    $page = visit('/login');

    $page
        ->assertSee('Sign In with Magic Link')
        ->assertSee('Enter your email to receive a magic link')
        ->assertSee('Email')
        ->assertSee('Send Magic Link')
        ->assertSee('Use Password')
        ->assertNoJavascriptErrors();
});

it('can toggle to password login mode', function (): void {
    $page = visit('/login');

    $page
        ->assertSee('Sign In with Magic Link')
        ->click('#login-method-toggle')
        ->assertSee('Sign In with Password')
        ->assertSee('Enter your email and password to sign in')
        ->assertSee('Password')
        ->assertSee('Sign In')
        ->assertSee('Use Magic Link');
});

it('can toggle back to magic link mode', function (): void {
    $page = visit('/login');

    $page
        ->click('#login-method-toggle')
        ->assertSee('Sign In with Password')
        ->click('#login-method-toggle')
        ->assertSee('Sign In with Magic Link')
        ->assertSee('Send Magic Link');
});

it('can send magic link for existing user', function (): void {
    Notification::fake();

    $user = User::factory()->create();

    $page = visit('/login')
        ->fill('email', $user->email)
        ->click('Send Magic Link')
        ->assertSee(
            "We've sent a magic link to your email address. Please check your inbox.",
        );

    Notification::assertSentTo($user, MagicLinkNotification::class);
});

it('shows error for non-existent email in magic link', function (): void {
    $page = visit('/login');

    $page
        ->fill('email', 'nonexistent@example.com')
        ->click('Send Magic Link')
        ->assertSee("couldn't find an account");
});

it('can login with password for existing user', function (): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password',
        'name' => 'Test User',
    ]);

    $page = visit('/login');

    $page
        ->click('#login-method-toggle')
        ->fill('email', 'test@example.com')
        ->fill('password', 'password')
        ->click('Sign In')
        ->assertPathIs('/dashboard')
        ->assertSee('Welcome to your dashboard');
});

it('shows error for invalid password login', function (): void {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $page = visit('/login');

    $page
        ->click('#login-method-toggle')
        ->fill('email', 'test@example.com')
        ->fill('password', 'wrongpassword')
        ->click('Sign In')
        ->assertSee('The provided credentials do not match our records');
});

it('greets admin user with Hello after login', function (): void {
    $adminRole = Role::query()->where('name', 'admin')->first();
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => 'password',
        'name' => 'Admin User',
    ]);
    $user->roles()->attach($adminRole);

    $page = visit('/login');

    $page
        ->click('#login-method-toggle')
        ->waitForText('Sign In')
        ->fill('email', 'admin@example.com')
        ->fill('password', 'password')
        ->click('Sign In')
        ->assertPathIs('/admin/dashboard')
        ->assertSee('Hello, Admin User');
});

it('greets employee user with Hi after login', function (): void {
    $employeeRole = Role::query()->where('name', 'employee')->first();
    $user = User::factory()->create([
        'email' => 'employee@example.com',
        'password' => 'password',
        'name' => 'Employee User',
    ]);
    $user->roles()->attach($employeeRole);

    $page = visit('/login');

    $page
        ->click('#login-method-toggle')
        ->waitForText('Sign In')
        ->fill('email', 'employee@example.com')
        ->fill('password', 'password')
        ->click('Sign In')
        ->assertPathIs('/dashboard')
        ->assertSee('Hi, Employee User');
});

it('can logout from dashboard', function (): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password',
        'name' => 'Test User',
    ]);

    $page = visit('/login');

    $page
        ->click('#login-method-toggle')
        ->fill('email', 'test@example.com')
        ->fill('password', 'password')
        ->click('Sign In')
        ->assertPathIs('/dashboard')
        ->assertSee('Welcome to your dashboard')
        ->click('Sign Out')
        ->assertPathIs('/login');
});
