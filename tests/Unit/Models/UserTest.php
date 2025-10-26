<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

it('has roles relationship with correct configuration', function (): void {
    $user = new User();
    $relationship = $user->roles();

    expect($relationship)->toBeInstanceOf(BelongsToMany::class);
    expect($relationship->getRelated())->toBeInstanceOf(Role::class);
    expect($relationship->getTable())->toBe('user_roles');
});

it('can manage roles through pivot relationship', function (): void {
    $user = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'admin']);
    $employeeRole = Role::factory()->create(['name' => 'employee']);

    // Test attach
    $user->roles()->attach($adminRole->id);
    expect($user->roles)->toHaveCount(1);
    expect($user->roles->first()->name)->toBe('admin');

    // Test sync (replace existing)
    $user->roles()->sync([$employeeRole->id]);
    $user->refresh();

    expect($user->roles)->toHaveCount(1);
    expect($user->roles->first()->name)->toBe('employee');

    // Test detach
    $user->roles()->detach($employeeRole->id);
    $user->refresh();

    expect($user->roles)->toHaveCount(0);
});

it('hashes password when set', function (): void {
    $user = User::factory()->make(['password' => 'plain-password']);

    expect($user->password)->not->toBe('plain-password');
    expect(mb_strlen((string) $user->password))->toBeGreaterThan(50);
});
