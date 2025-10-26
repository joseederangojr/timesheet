<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

it('has users relationship with correct configuration', function (): void {
    $role = new Role();
    $relationship = $role->users();

    expect($relationship)->toBeInstanceOf(BelongsToMany::class);
    expect($relationship->getRelated())->toBeInstanceOf(User::class);
    expect($relationship->getTable())->toBe('user_roles');
});

it(
    'maintains bidirectional many-to-many relationship integrity',
    function (): void {
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $employeeRole = Role::factory()->create(['name' => 'employee']);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Attach roles to users
        $user1->roles()->attach([$adminRole->id, $employeeRole->id]);
        $user2->roles()->attach([$employeeRole->id]);

        // Verify from user perspective
        expect($user1->roles)->toHaveCount(2);
        expect($user2->roles)->toHaveCount(1);
        expect($user1->roles->pluck('name'))->toContain('admin', 'employee');
        expect($user2->roles->pluck('name'))->toContain('employee');

        // Verify from role perspective
        expect($adminRole->users)->toHaveCount(1);
        expect($employeeRole->users)->toHaveCount(2);
        expect($adminRole->users->first()->id)->toBe($user1->id);
        expect($employeeRole->users->pluck('id'))->toContain(
            $user1->id,
            $user2->id,
        );
    },
);
