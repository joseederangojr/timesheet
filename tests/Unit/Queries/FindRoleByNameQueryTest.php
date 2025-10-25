<?php

declare(strict_types=1);

use App\Models\Role;
use App\Queries\FindRoleByNameQuery;

it('returns role when found', function (): void {
    $role = Role::factory()->create(['name' => 'admin']);

    $query = new FindRoleByNameQuery();
    $result = $query->handle('admin');

    expect($result)->toBeInstanceOf(Role::class);
    expect($result->id)->toBe($role->id);
    expect($result->name)->toBe('admin');
});

it('returns null when role not found', function (): void {
    $query = new FindRoleByNameQuery();
    $result = $query->handle('nonexistent');

    expect($result)->toBeNull();
});

it('is case sensitive', function (): void {
    Role::factory()->create(['name' => 'admin']);

    $query = new FindRoleByNameQuery();
    $result = $query->handle('Admin');

    expect($result)->toBeNull();
});

it('returns first role when multiple roles exist', function (): void {
    $firstRole = Role::factory()->create(['name' => 'admin']);
    Role::factory()->create(['name' => 'editor']);

    $query = new FindRoleByNameQuery();
    $result = $query->handle('admin');

    expect($result)->toBeInstanceOf(Role::class);
    expect($result->id)->toBe($firstRole->id);
});
