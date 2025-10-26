<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed();
});

it('shows dashboard for authenticated regular users', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('dashboard'));
});

it('shows admin dashboard for authenticated admin users', function (): void {
    $adminRole = Role::query()->where('name', 'admin')->first();
    $user = User::factory()->create(['name' => 'Admin User']);
    $user->roles()->attach($adminRole);

    $response = $this->actingAs($user)
        ->withSession(['greeting' => 'Hello, Admin User'])
        ->get('/admin/dashboard');

    $response
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('admin/dashboard')
                ->has('greeting')
                ->where('greeting', 'Hello, Admin User')
                ->has('auth.user.name')
                ->where('auth.user.name', 'Admin User'),
        );
});

it('redirects unauthenticated users to login', function (): void {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->get('/admin/dashboard')->assertRedirect('/login');
});

it('displays greeting message when present in session', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['greeting' => 'Hello, Test User'])
        ->get(route('dashboard'))
        ->assertSuccessful();
});
