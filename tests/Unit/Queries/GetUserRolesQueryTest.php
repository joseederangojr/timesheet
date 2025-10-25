<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use App\Queries\GetUserRolesQuery;

it('returns user role names as array', function (): void {
    $user = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'admin']);
    $editorRole = Role::factory()->create(['name' => 'editor']);

    $user->roles()->attach([$adminRole->id, $editorRole->id]);

    $query = new GetUserRolesQuery();
    $result = $query->handle($user);

    expect($result)->toBeArray();
    expect($result)->toContain('admin');
    expect($result)->toContain('editor');
    expect($result)->toHaveCount(2);
});

it('returns empty array when user has no roles', function (): void {
    $user = User::factory()->create();

    $query = new GetUserRolesQuery();
    $result = $query->handle($user);

    expect($result)->toBeArray();
    expect($result)->toBeEmpty();
});

it('returns single role name in array', function (): void {
    $user = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'admin']);

    $user->roles()->attach($adminRole);

    $query = new GetUserRolesQuery();
    $result = $query->handle($user);

    expect($result)->toBeArray();
    expect($result)->toEqual(['admin']);
    expect($result)->toHaveCount(1);
});

it('returns role names in consistent order', function (): void {
    $user = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'admin']);
    $editorRole = Role::factory()->create(['name' => 'editor']);
    $viewerRole = Role::factory()->create(['name' => 'viewer']);

    $user->roles()->attach([$adminRole->id, $editorRole->id, $viewerRole->id]);

    $query = new GetUserRolesQuery();
    $result1 = $query->handle($user);
    $result2 = $query->handle($user);

    expect($result1)->toEqual($result2);
});
