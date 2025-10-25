<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

uses(RefreshDatabase::class, WithoutMiddleware::class);

beforeEach(function (): void {
    $this->seed();
});

it('logs out authenticated user and redirects to login', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

it('requires authentication to access logout route', function (): void {
    $this->post(route('logout'))->assertRedirect(route('login'));
});
