<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed();
});

it('shows admin dashboard for authenticated admin users', function (): void {
    $adminRole = Role::query()->where('name', 'admin')->first();
    $user = User::factory()->create([
        'name' => 'Admin User',
    ]);
    $user->roles()->attach($adminRole);

    $response = $this->actingAs($user)->withSession(['greeting' => 'Hello, Admin User'])
        ->get('/admin/dashboard');

    $response->assertOk();
    $response->assertInertia(
        fn (Inertia\Testing\AssertableInertia $page): Inertia\Testing\AssertableInertia => $page
            ->component('admin/Dashboard')
            ->has('greeting')
            ->where('greeting', 'Hello, Admin User')
            ->has('auth.user.name')
            ->where('auth.user.name', 'Admin User')
    );
});

it('redirects unauthenticated users to login', function (): void {
    $response = $this->get('/admin/dashboard');

    $response->assertRedirect('/login');
});
