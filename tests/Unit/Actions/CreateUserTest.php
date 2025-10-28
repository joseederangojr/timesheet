<?php

declare(strict_types=1);

use App\Actions\CreateUser;
use App\Data\CreateUserData;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->action = app(CreateUser::class);
});

it('creates user successfully', function (): void {
    $userRole = Role::factory()->create(['name' => 'user']);

    $data = new CreateUserData(
        name: 'John Doe',
        email: 'john@example.com',
        password: 'password123',
        roles: new Collection([$userRole]),
    );

    $user = $this->action->handle($data);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe('John Doe');
    expect($user->email)->toBe('john@example.com');
    expect($user->roles->pluck('name'))->toContain('user');

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

it('creates user with roles attached', function (): void {
    $adminRole = Role::factory()->create(['name' => 'admin']);
    $userRole = Role::factory()->create(['name' => 'user']);

    $data = new CreateUserData(
        name: 'Jane Smith',
        email: 'jane@example.com',
        password: 'password123',
        roles: new Collection([$adminRole, $userRole]),
    );

    $user = $this->action->handle($data);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe('Jane Smith');
    expect($user->email)->toBe('jane@example.com');
    expect($user->roles->pluck('name')->sort())->toEqual(
        collect(['admin', 'user'])->sort(),
    );

    $this->assertDatabaseHas('users', [
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
    ]);
    $this->assertDatabaseHas('user_roles', [
        'user_id' => $user->id,
        'role_id' => $adminRole->id,
    ]);
    $this->assertDatabaseHas('user_roles', [
        'user_id' => $user->id,
        'role_id' => $userRole->id,
    ]);
});
