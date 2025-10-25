<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use App\Queries\CheckUserHasAnyRoleQuery;

it('returns true when user has any of the specified roles', function (): void {
    $user = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'admin']);
    $editorRole = Role::factory()->create(['name' => 'editor']);

    $user->roles()->attach($adminRole);

    $query = new CheckUserHasAnyRoleQuery();
    $result = $query->handle($user, ['admin', 'editor']);

    expect($result)->toBeTrue();
});

it(
    'returns false when user does not have any of the specified roles',
    function (): void {
        $user = User::factory()->create();
        Role::factory()->create(['name' => 'viewer']);

        $query = new CheckUserHasAnyRoleQuery();
        $result = $query->handle($user, ['admin', 'editor']);

        expect($result)->toBeFalse();
    },
);

it('returns false when user has no roles', function (): void {
    $user = User::factory()->create();

    $query = new CheckUserHasAnyRoleQuery();
    $result = $query->handle($user, ['admin', 'editor']);

    expect($result)->toBeFalse();
});

it('returns true when user has multiple matching roles', function (): void {
    $user = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'admin']);
    $editorRole = Role::factory()->create(['name' => 'editor']);

    $user->roles()->attach([$adminRole->id, $editorRole->id]);

    $query = new CheckUserHasAnyRoleQuery();
    $result = $query->handle($user, ['admin', 'editor']);

    expect($result)->toBeTrue();
});

it('handles empty role names array', function (): void {
    $user = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'admin']);

    $user->roles()->attach($adminRole);

    $query = new CheckUserHasAnyRoleQuery();
    $result = $query->handle($user, []);

    expect($result)->toBeFalse();
});
