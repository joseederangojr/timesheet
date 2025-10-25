<?php

declare(strict_types=1);

use App\Models\User;

it('shows index page for authenticated users', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/')
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('index'));
});

it('shows index page for unauthenticated users', function (): void {
    $this->get('/')
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('index'));
});

it('returns successful response when accessed directly', function (): void {
    $this->get('/')->assertStatus(200);
});
