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

    $page = visit('/admin/users');

    $page
        ->assertSee('Users')
        ->assertSee('Manage system users and their roles')
        ->assertSee('Search by name or email')
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

    $user1 = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
    $user2 = User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

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

    $user1 = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
    $user2 = User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);
    $user3 = User::factory()->create(['name' => 'Bob Johnson', 'email' => 'bob@example.com']);

    $this->actingAs($admin);

    $page = visit('/admin/users');

    // Search for "John"
    $page->fill('[placeholder="Search by name or email"]', 'John');

    // Wait for search to apply
    $page->waitForText('John Doe');

    $page
        ->assertSee('John Doe')
        ->assertSee('Bob Johnson') // contains "John"
        ->assertDontSee('Jane Smith');
});

it('filters users by role', function (): void {
    $adminRole = resolve(FindRoleByNameQuery::class)->handle('admin');
    $employeeRole = resolve(FindRoleByNameQuery::class)->handle('employee');

    $adminUser = User::factory()->hasAttached($adminRole)->create(['name' => 'Admin User']);
    $employeeUser = User::factory()->hasAttached($employeeRole)->create(['name' => 'Employee User']);
    $noRoleUser = User::factory()->create(['name' => 'No Role User']);

    $this->actingAs($adminUser);

    $page = visit('/admin/users');

    // Filter by admin role
    $page->click('[role="combobox"][aria-label="Role"]');
    $page->waitForText('Admin');
    $page->click('Admin');

    $page
        ->waitForText('Admin User')
        ->assertSee('Admin User')
        ->assertDontSee('Employee User')
        ->assertDontSee('No Role User');
});

it('filters users by verification status', function (): void {
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

    // Filter by verified
    $page->click('[role="combobox"][aria-label="Verified"]');
    $page->waitForText('Verified');
    $page->click('Verified');

    $page
        ->waitForText('Verified User')
        ->assertSee('Verified User')
        ->assertDontSee('Unverified User');
});

it('sorts users by name', function (): void {
    $role = resolve(FindRoleByNameQuery::class)->handle('admin');
    $admin = User::factory()->hasAttached($role)->create();

    $user1 = User::factory()->create(['name' => 'Alice Brown']);
    $user2 = User::factory()->create(['name' => 'Bob Smith']);
    $user3 = User::factory()->create(['name' => 'Charlie Wilson']);

    $this->actingAs($admin);

    $page = visit('/admin/users');

    // Click the User column header to sort
    $page->click('button:has-text("User")');

    // Should be sorted ascending by default
    $page->assertSeeInOrder(['Alice Brown', 'Bob Smith', 'Charlie Wilson']);
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

    // Should show 15 users on first page
    $page->assertSee('Page 1 of');

    // Check if there are 15 rows (plus header)
    $rows = $page->crawler()->filter('tbody tr');
    expect($rows->count())->toBe(15);
});

it('shows user roles in badges', function (): void {
    $adminRole = resolve(FindRoleByNameQuery::class)->handle('admin');
    $employeeRole = resolve(FindRoleByNameQuery::class)->handle('employee');

    $adminUser = User::factory()->hasAttached($adminRole)->create(['name' => 'Admin User']);
    $employeeUser = User::factory()->hasAttached($employeeRole)->create(['name' => 'Employee User']);
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

    $page
        ->assertSee('Verified')
        ->assertSee('Unverified');
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