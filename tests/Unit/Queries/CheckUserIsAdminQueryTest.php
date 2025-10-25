<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use App\Queries\CheckUserHasRoleQuery;
use App\Queries\CheckUserIsAdminQuery;

it('returns true when user is an admin', function (): void {
    $user = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'admin']);

    $user->roles()->attach($adminRole);

    $checkUserHasRoleQuery = new CheckUserHasRoleQuery();
    $query = new CheckUserIsAdminQuery($checkUserHasRoleQuery);
    $result = $query->handle($user);

    expect($result)->toBeTrue();
});

it('returns false when user is not an admin', function (): void {
    $user = User::factory()->create();
    $editorRole = Role::factory()->create(['name' => 'editor']);

    $user->roles()->attach($editorRole);

    $checkUserHasRoleQuery = new CheckUserHasRoleQuery();
    $query = new CheckUserIsAdminQuery($checkUserHasRoleQuery);
    $result = $query->handle($user);

    expect($result)->toBeFalse();
});

it('returns false when user has no roles', function (): void {
    $user = User::factory()->create();

    $checkUserHasRoleQuery = new CheckUserHasRoleQuery();
    $query = new CheckUserIsAdminQuery($checkUserHasRoleQuery);
    $result = $query->handle($user);

    expect($result)->toBeFalse();
});

it('uses dependency injection correctly', function (): void {
    $user = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'admin']);
    $user->roles()->attach($adminRole);

    $checkUserHasRoleQuery = new CheckUserHasRoleQuery();
    $query = new CheckUserIsAdminQuery($checkUserHasRoleQuery);

    expect($query)->toBeInstanceOf(CheckUserIsAdminQuery::class);
    expect($query->handle($user))->toBeTrue();
});
