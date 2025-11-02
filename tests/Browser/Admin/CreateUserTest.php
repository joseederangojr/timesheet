<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->adminRole = Role::factory()->create(['name' => 'admin']);
    $this->employeeRole = Role::factory()->create(['name' => 'employee']);

    $this->admin = User::factory()->create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);
    $this->admin->roles()->attach($this->adminRole);
});

describe('Admin Create User', function (): void {
    it('can create user via modal', function (): void {
        $userRole = Role::factory()->create(['name' => 'user']);

        $this->actingAs($this->admin)
            ->visit('/admin/users')
            ->click('Add User')
            ->waitForText('Create New User')
            ->type('name', 'New User Modal')
            ->type('email', 'modal@example.com')
            ->type('password', 'password123')
            ->press('Select roles...')
            ->wait(0.1)
            ->press('employee')
            ->wait(0.1)
            ->assertSee('employee')
            ->press('Create User')
            ->wait(0.1)
            ->assertPathIs('/admin/users')
            ->wait(0.1)
            ->assertSee('User created successfully.');

        $this->assertDatabaseHas('users', [
            'name' => 'New User Modal',
            'email' => 'modal@example.com',
        ]);

        $newUser = User::query()->where('email', 'modal@example.com')->first();
        expect($newUser->roles->pluck('name'))->toContain('employee');
    });

    it('can create user via page', function (): void {
        $managerRole = Role::factory()->create(['name' => 'manager']);

        $this->actingAs($this->admin)
            ->visit('/admin/users/create')
            ->type('name', 'New User Page')
            ->type('email', 'page@example.com')
            ->type('password', 'password123')
            ->press('Select roles...')
            ->wait(0.1)
            ->press('admin')
            ->wait(0.1)
            ->assertSee('admin')
            ->press('Create User')
            ->wait(0.1)
            ->assertPathIs('/admin/users')
            ->wait(0.1)
            ->assertSee('User created successfully.');

        $this->assertDatabaseHas('users', [
            'name' => 'New User Page',
            'email' => 'page@example.com',
        ]);

        $newUser = User::query()->where('email', 'page@example.com')->first();
        expect($newUser->roles->pluck('name'))->toContain('admin');
    });

    it('requires admin authentication for modal', function (): void {
        $this->visit('/admin/users')->assertPathIs('/login');
    });

    it('requires admin authentication for page', function (): void {
        $this->visit('/admin/users/create')->assertPathIs('/login');
    });
});
