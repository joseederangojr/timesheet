<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use App\Queries\CheckUserHasRoleQuery;
use App\Queries\CheckUserIsEmployeeQuery;

it('returns true when user is an employee', function (): void {
    $user = User::factory()->create();
    $employeeRole = Role::factory()->create(['name' => 'employee']);

    $user->roles()->attach($employeeRole);

    $checkUserHasRoleQuery = new CheckUserHasRoleQuery();
    $query = new CheckUserIsEmployeeQuery($checkUserHasRoleQuery);
    $result = $query->handle($user);

    expect($result)->toBeTrue();
});

it('returns false when user is not an employee', function (): void {
    $user = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'admin']);

    $user->roles()->attach($adminRole);

    $checkUserHasRoleQuery = new CheckUserHasRoleQuery();
    $query = new CheckUserIsEmployeeQuery($checkUserHasRoleQuery);
    $result = $query->handle($user);

    expect($result)->toBeFalse();
});

it('returns false when user has no roles', function (): void {
    $user = User::factory()->create();

    $checkUserHasRoleQuery = new CheckUserHasRoleQuery();
    $query = new CheckUserIsEmployeeQuery($checkUserHasRoleQuery);
    $result = $query->handle($user);

    expect($result)->toBeFalse();
});

it('uses dependency injection correctly', function (): void {
    $user = User::factory()->create();
    $employeeRole = Role::factory()->create(['name' => 'employee']);
    $user->roles()->attach($employeeRole);

    $checkUserHasRoleQuery = new CheckUserHasRoleQuery();
    $query = new CheckUserIsEmployeeQuery($checkUserHasRoleQuery);

    expect($query)->toBeInstanceOf(CheckUserIsEmployeeQuery::class);
    expect($query->handle($user))->toBeTrue();
});
