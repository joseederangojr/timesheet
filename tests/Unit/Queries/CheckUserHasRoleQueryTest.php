<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use App\Queries\CheckUserHasRoleQuery;

it('returns true when user has the specified role', function (): void {
    $user = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'admin']);

    $user->roles()->attach($adminRole);

    $query = new CheckUserHasRoleQuery();
    $result = $query->handle($user, 'admin');

    expect($result)->toBeTrue();
});

it(
    'returns false when user does not have the specified role',
    function (): void {
        $user = User::factory()->create();
        Role::factory()->create(['name' => 'admin']);

        $query = new CheckUserHasRoleQuery();
        $result = $query->handle($user, 'admin');

        expect($result)->toBeFalse();
    },
);

it('returns false when user has different roles', function (): void {
    $user = User::factory()->create();
    $editorRole = Role::factory()->create(['name' => 'editor']);

    $user->roles()->attach($editorRole);

    $query = new CheckUserHasRoleQuery();
    $result = $query->handle($user, 'admin');

    expect($result)->toBeFalse();
});

it('returns false when user has no roles', function (): void {
    $user = User::factory()->create();

    $query = new CheckUserHasRoleQuery();
    $result = $query->handle($user, 'admin');

    expect($result)->toBeFalse();
});

it('is case sensitive', function (): void {
    $user = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'admin']);

    $user->roles()->attach($adminRole);

    $query = new CheckUserHasRoleQuery();
    $result = $query->handle($user, 'Admin');

    expect($result)->toBeFalse();
});
