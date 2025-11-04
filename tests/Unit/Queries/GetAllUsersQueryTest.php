<?php

declare(strict_types=1);

use App\Models\User;
use App\Queries\GetAllUsersQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns all users ordered by name', function (): void {
    // Create test users
    User::factory()->create(['name' => 'Charlie Brown']);
    User::factory()->create(['name' => 'Alice Smith']);
    User::factory()->create(['name' => 'Bob Johnson']);

    $query = app(GetAllUsersQuery::class);
    $users = $query->handle();

    expect($users)->toHaveCount(3);
    expect($users->pluck('name')->toArray())->toEqual([
        'Alice Smith',
        'Bob Johnson',
        'Charlie Brown',
    ]);

    // Verify only id, name, email are selected
    $user = $users->first();
    expect($user)->toHaveKeys(['id', 'name', 'email']);
});

it('returns empty collection when no users exist', function (): void {
    // Delete all users first
    User::query()->delete();

    $query = app(GetAllUsersQuery::class);
    $users = $query->handle();

    expect($users)->toBeEmpty();
});

it('returns users with correct model instances', function (): void {
    User::factory()->create();

    $query = app(GetAllUsersQuery::class);
    $users = $query->handle();

    expect($users->first())->toBeInstanceOf(User::class);
});
