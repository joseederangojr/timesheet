<?php

declare(strict_types=1);

use App\Models\User;

it('shows dashboard for authenticated users', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertInertia(fn($page) => $page->component('Dashboard'));
});

it('redirects unauthenticated users to login', function (): void {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('displays greeting message when present in session', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['greeting' => 'Hello, Test User'])
        ->get(route('dashboard'))
        ->assertSuccessful();
});
