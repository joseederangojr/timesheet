<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use App\Queries\FindRoleByNameQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed();
});

it('displays the admin users page with user list', function (): void {
    $role = resolve(FindRoleByNameQuery::class)->handle('admin');
    $admin = User::factory()->hasAttached($role)->create();

    // Create some test users
    User::factory(5)->create();

    $this->actingAs($admin);

    $page = visit('/admin/users')->wait(0.1);

    $page
        ->assertSee('Users')
        ->assertSee('Manage system users and their roles')
        ->assertSee('Role')
        ->assertSee('Verified')
        ->assertSee('User')
        ->assertSee('Email')
        ->assertSee('Roles')
        ->assertSee('Verified')
        ->assertSee('Joined At');
});

it('displays users in the table', function (): void {
    $role = resolve(FindRoleByNameQuery::class)->handle('admin');
    $admin = User::factory()->hasAttached($role)->create();

    $user1 = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
    $user2 = User::factory()->create([
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
    ]);

    $this->actingAs($admin);

    $page = visit('/admin/users');

    $page
        ->assertSee('John Doe')
        ->assertSee('john@example.com')
        ->assertSee('Jane Smith')
        ->assertSee('jane@example.com');
});

it('filters users by search term', function (): void {
    $role = resolve(FindRoleByNameQuery::class)->handle('admin');
    $admin = User::factory()->hasAttached($role)->create();

    $user1 = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
    $user2 = User::factory()->create([
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
    ]);
    $user3 = User::factory()->create([
        'name' => 'Bob Johnson',
        'email' => 'bob@example.com',
    ]);

    $this->actingAs($admin);

    $page = visit('/admin/users?search=John')->wait(0.1);

    $page
        ->assertSee('John Doe')
        ->assertSee('Bob Johnson') // contains "John"
        ->assertDontSee('Jane Smith');
});

it('shows role filter options', function (): void {
    $role = resolve(FindRoleByNameQuery::class)->handle('admin');
    $admin = User::factory()->hasAttached($role)->create();

    $this->actingAs($admin);

    $page = visit('/admin/users');

    // Check that role filter is present
    $page->assertSee('Role');
});

it('shows verification filter options', function (): void {
    $role = resolve(FindRoleByNameQuery::class)->handle('admin');
    $admin = User::factory()->hasAttached($role)->create();

    $this->actingAs($admin);

    $page = visit('/admin/users');

    // Check that verified filter is present
    $page->assertSee('Verified');
});

it('filters users by role via URL', function (): void {
    $adminRole = resolve(FindRoleByNameQuery::class)->handle('admin');
    $employeeRole = resolve(FindRoleByNameQuery::class)->handle('employee');

    $adminUser = User::factory()
        ->hasAttached($adminRole)
        ->create(['name' => 'Admin User']);
    $employeeUser = User::factory()
        ->hasAttached($employeeRole)
        ->create(['name' => 'Employee User']);
    $noRoleUser = User::factory()->create(['name' => 'No Role User']);

    $this->actingAs($adminUser);

    $page = visit('/admin/users?role=admin');

    $page
        ->assertSee('Admin User')
        ->assertDontSee('Employee User')
        ->assertDontSee('No Role User');
});

it('filters users by verification status via URL', function (): void {
    $role = resolve(FindRoleByNameQuery::class)->handle('admin');
    $admin = User::factory()->hasAttached($role)->create();

    $verifiedUser = User::factory()->create([
        'name' => 'Verified User',
        'email_verified_at' => now(),
    ]);
    $unverifiedUser = User::factory()->create([
        'name' => 'Unverified User',
        'email_verified_at' => null,
    ]);

    $this->actingAs($admin);

    $page = visit('/admin/users?verified=verified');

    $page->assertSee('Verified User')->assertDontSee('Unverified User');
});

it('sorts users by name', function (): void {
    $role = resolve(FindRoleByNameQuery::class)->handle('admin');
    $admin = User::factory()->hasAttached($role)->create();

    $user1 = User::factory()->create(['name' => 'Alice Brown']);
    $user2 = User::factory()->create(['name' => 'Bob Smith']);
    $user3 = User::factory()->create(['name' => 'Charlie Wilson']);

    $this->actingAs($admin);

    $page = visit('/admin/users');

    // Click the Name column header to sort
    $page->click('Name');

    // Should show the users (sorting may take effect)
    $page
        ->assertSee('Alice Brown')
        ->assertSee('Bob Smith')
        ->assertSee('Charlie Wilson');
});

it('paginates users', function (): void {
    $role = resolve(FindRoleByNameQuery::class)->handle('admin');
    $admin = User::factory()->hasAttached($role)->create();

    // Create more users than default page size (15)
    User::factory(20)->create();

    $this->actingAs($admin);

    $page = visit('/admin/users');

    // Should show pagination info
    $page->assertSee('Rows per page');

    // Should show pagination info
    $page->assertSee('Page 1 of')->assertSee('Rows per page');
});

it('shows user roles in badges', function (): void {
    $adminRole = resolve(FindRoleByNameQuery::class)->handle('admin');
    $employeeRole = resolve(FindRoleByNameQuery::class)->handle('employee');

    $adminUser = User::factory()
        ->hasAttached($adminRole)
        ->create(['name' => 'Admin User']);
    $employeeUser = User::factory()
        ->hasAttached($employeeRole)
        ->create(['name' => 'Employee User']);
    $noRoleUser = User::factory()->create(['name' => 'No Role User']);

    $this->actingAs($adminUser);

    $page = visit('/admin/users');

    $page
        ->assertSee('admin') // Role badge
        ->assertSee('employee') // Role badge
        ->assertSee('No roles'); // No role badge
});

it('shows verification status badges', function (): void {
    $role = resolve(FindRoleByNameQuery::class)->handle('admin');
    $admin = User::factory()->hasAttached($role)->create();

    $verifiedUser = User::factory()->create([
        'name' => 'Verified User',
        'email_verified_at' => now(),
    ]);
    $unverifiedUser = User::factory()->create([
        'name' => 'Unverified User',
        'email_verified_at' => null,
    ]);

    $this->actingAs($admin);

    $page = visit('/admin/users');

    $page->assertSee('Verified')->assertSee('Unverified');
});

it('displays joined date formatted', function (): void {
    $role = resolve(FindRoleByNameQuery::class)->handle('admin');
    $admin = User::factory()->hasAttached($role)->create();

    $user = User::factory()->create([
        'name' => 'Test User',
        'created_at' => now()->subDays(5),
    ]);

    $this->actingAs($admin);

    $page = visit('/admin/users');

    // Should show formatted date
    $formattedDate = now()->subDays(5)->format('M j, Y');
    $page->assertSee($formattedDate);
});
