<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it(
    'redirects to dashboard with admin greeting after successful login',
    function () {
        $adminRole = Role::query()->where('name', 'admin')->first();
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
            'name' => 'Admin User',
        ]);
        $user->roles()->attach($adminRole);

        $response = $this->withSession(['_token' => 'test-token'])->post(
            '/login/password',
            [
                '_token' => 'test-token',
                'email' => 'admin@example.com',
                'password' => 'password',
            ],
        );

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('greeting', 'Hello, Admin User');
        $this->assertAuthenticatedAs($user);
    },
);

it(
    'redirects to dashboard with employee greeting after successful login',
    function () {
        $employeeRole = Role::query()->where('name', 'employee')->first();
        $user = User::factory()->create([
            'email' => 'employee@example.com',
            'password' => 'password',
            'name' => 'Employee User',
        ]);
        $user->roles()->attach($employeeRole);

        $response = $this->withSession(['_token' => 'test-token'])->post(
            '/login/password',
            [
                '_token' => 'test-token',
                'email' => 'employee@example.com',
                'password' => 'password',
            ],
        );

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('greeting', 'Hi, Employee User');
        $this->assertAuthenticatedAs($user);
    },
);

it('returns error for invalid credentials', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response = $this->withSession(['_token' => 'test-token'])->post(
        '/login/password',
        [
            '_token' => 'test-token',
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ],
    );

    $response->assertRedirect();
    $response->assertSessionHasErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
    $this->assertGuest();
});

it('validates required fields', function () {
    $response = $this->withSession(['_token' => 'test-token'])->post(
        '/login/password',
        [
            '_token' => 'test-token',
        ],
    );

    $response->assertRedirect();
    $response->assertSessionHasErrors(['email', 'password']);
});
