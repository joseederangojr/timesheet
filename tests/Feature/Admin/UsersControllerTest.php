<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

describe('Admin Users Controller', function (): void {
    beforeEach(function (): void {
        Notification::fake();

        $this->adminRole = Role::factory()->create(['name' => 'admin']);
        $this->employeeRole = Role::factory()->create(['name' => 'employee']);

        $this->admin = User::factory()->create(['name' => 'Admin User']);
        $this->admin->roles()->attach($this->adminRole);
    });

    describe('index', function (): void {
        it('displays users list for authenticated admin', function (): void {
            $users = User::factory(5)->create();

            foreach ($users as $user) {
                $user->roles()->attach($this->employeeRole);
            }

            $response = $this->actingAs($this->admin)->get('/admin/users');

            expect($response)->assertSuccessful()->assertInertia(
                fn ($page) => $page
                    ->component('admin/users/index')
                    ->has('users.data', 6) // 5 created + 1 admin
                    ->has('filters'),
            );
        });

        it('requires authentication to access users list', function (): void {
            $response = $this->get('/admin/users');

            expect($response)->assertRedirect('/login');
        });

        it('can search users by name', function (): void {
            $john = User::factory()->create(['name' => 'Johnathan Unique']);
            $jane = User::factory()->create(['name' => 'Jane Smith']);

            $response = $this->actingAs($this->admin)->get(
                '/admin/users?search=Johnathan',
            );

            expect($response)
                ->assertSuccessful()
                ->assertInertia(
                    fn ($page) => $page
                        ->component('admin/users/index')
                        ->where('filters.search', 'Johnathan')
                        ->has('users.data', 1)
                        ->where('users.data.0.name', 'Johnathan Unique'),
                );
        });

        it('can search users by email', function (): void {
            $user1 = User::factory()->create(['email' => 'unique@testing.com']);
            $user2 = User::factory()->create([
                'email' => 'other@different.com',
            ]);

            $response = $this->actingAs($this->admin)->get(
                '/admin/users?search=unique@testing',
            );

            expect($response)
                ->assertSuccessful()
                ->assertInertia(
                    fn ($page) => $page
                        ->component('admin/users/index')
                        ->where('filters.search', 'unique@testing')
                        ->has('users.data', 1)
                        ->where('users.data.0.email', 'unique@testing.com'),
                );
        });

        it('returns paginated results', function (): void {
            User::factory(20)->create();

            $response = $this->actingAs($this->admin)->get('/admin/users');

            expect($response)->assertSuccessful()->assertInertia(
                fn ($page) => $page
                    ->component('admin/users/index')
                    ->has('users.data', 15) // Default pagination is 15
                    ->where('users.current_page', 1)
                    ->where('users.per_page', 15)
                    ->where('users.total', 21), // 20 created + 1 admin
            );
        });

        it('loads users with their roles', function (): void {
            $user = User::factory()->create();
            $role = Role::factory()->create(['name' => 'manager']);
            $user->roles()->attach($role);

            $response = $this->actingAs($this->admin)->get('/admin/users');

            expect($response)
                ->assertSuccessful()
                ->assertInertia(
                    fn ($page) => $page
                        ->component('admin/users/index')
                        ->has(
                            'users.data.1.roles.0',
                            fn ($roleData) => $roleData
                                ->where('name', 'manager')
                                ->etc(),
                        ),
                );
        });

        it('can sort users by name ascending', function (): void {
            $userZ = User::factory()->create(['name' => 'Zoe Last']);
            $userA = User::factory()->create(['name' => 'Alice First']);

            $response = $this->actingAs($this->admin)->get(
                '/admin/users?sort_by=name&sort_direction=asc',
            );

            expect($response)->assertSuccessful()->assertInertia(
                fn ($page) => $page
                    ->component('admin/users/index')
                    ->where('filters.sort_by', 'name')
                    ->where('filters.sort_direction', 'asc')
                    ->has('users.data', 3) // admin + 2 created
                    ->where('users.data.0.name', 'Admin User'), // Admin User comes first alphabetically
            );
        });

        it('can sort users by name descending', function (): void {
            $userZ = User::factory()->create(['name' => 'Zoe Last']);
            $userA = User::factory()->create(['name' => 'Alice First']);

            $response = $this->actingAs($this->admin)->get(
                '/admin/users?sort_by=name&sort_direction=desc',
            );

            expect($response)->assertSuccessful()->assertInertia(
                fn ($page) => $page
                    ->component('admin/users/index')
                    ->where('filters.sort_by', 'name')
                    ->where('filters.sort_direction', 'desc')
                    ->has('users.data', 3) // admin + 2 created
                    ->where('users.data.0.name', 'Zoe Last'),
            );
        });

        it('can sort users by email', function (): void {
            $userZ = User::factory()->create(['email' => 'z@example.com']);
            $userA = User::factory()->create(['email' => 'a@example.com']);

            $response = $this->actingAs($this->admin)->get(
                '/admin/users?sort_by=email&sort_direction=asc',
            );

            expect($response)->assertSuccessful()->assertInertia(
                fn ($page) => $page
                    ->component('admin/users/index')
                    ->where('filters.sort_by', 'email')
                    ->where('filters.sort_direction', 'asc')
                    ->has('users.data', 3) // admin + 2 created
                    ->where('users.data.0.email', 'a@example.com'),
            );
        });

        it('can sort users by creation date', function (): void {
            $oldUser = User::factory()->create();
            $oldUser->created_at = now()->subDays(2);
            $oldUser->save();

            $newUser = User::factory()->create();
            $newUser->created_at = now()->subDay();
            $newUser->save();

            $response = $this->actingAs($this->admin)->get(
                '/admin/users?sort_by=created_at&sort_direction=asc',
            );

            expect($response)->assertSuccessful()->assertInertia(
                fn ($page) => $page
                    ->component('admin/users/index')
                    ->where('filters.sort_by', 'created_at')
                    ->where('filters.sort_direction', 'asc')
                    ->has('users.data', 3) // admin + 2 created
                    ->where('users.data.0.id', $oldUser->id),
            );
        });

        it(
            'defaults to sorting by created_at desc when invalid sort field provided',
            function (): void {
                User::factory(2)->create();

                $response = $this->actingAs($this->admin)->get(
                    '/admin/users?sort_by=invalid_field&sort_direction=asc',
                );

                expect($response)
                    ->assertSuccessful()
                    ->assertInertia(
                        fn ($page) => $page
                            ->component('admin/users/index')
                            ->where('filters.sort_by', 'invalid_field')
                            ->where('filters.sort_direction', 'asc'),
                    );
            },
        );

        it(
            'defaults to desc direction when invalid direction provided',
            function (): void {
                User::factory(2)->create();

                $response = $this->actingAs($this->admin)->get(
                    '/admin/users?sort_by=name&sort_direction=invalid',
                );

                expect($response)
                    ->assertSuccessful()
                    ->assertInertia(
                        fn ($page) => $page
                            ->component('admin/users/index')
                            ->where('filters.sort_by', 'name')
                            ->where('filters.sort_direction', 'invalid'),
                    );
            },
        );

        it('can filter users by role', function (): void {
            $adminUser = User::factory()->create(['name' => 'Admin User']);
            $adminUser->roles()->attach($this->adminRole);

            $employeeUser = User::factory()->create([
                'name' => 'Employee User',
            ]);
            $employeeUser->roles()->attach($this->employeeRole);

            $response = $this->actingAs($this->admin)->get(
                '/admin/users?role=admin',
            );

            expect($response)->assertSuccessful()->assertInertia(
                fn ($page) => $page
                    ->component('admin/users/index')
                    ->where('filters.role', 'admin')
                    ->has('users.data', 2) // admin + adminUser
                    ->where('users.data.0.name', $this->admin->name) // admin is created first
                    ->where('users.data.1.name', 'Admin User'),
            );
        });

        it('can filter users by verified status', function (): void {
            $verifiedUser = User::factory()->create([
                'name' => 'Verified User',
                'email_verified_at' => now(),
            ]);

            $unverifiedUser = User::factory()->create([
                'name' => 'Unverified User',
                'email_verified_at' => null,
            ]);

            $response = $this->actingAs($this->admin)->get(
                '/admin/users?verified=verified',
            );

            expect($response)->assertSuccessful()->assertInertia(
                fn ($page) => $page
                    ->component('admin/users/index')
                    ->where('filters.verified', 'verified')
                    ->has('users.data', 2) // admin + verifiedUser
                    ->where('users.data.0.name', $this->admin->name) // admin is verified
                    ->where('users.data.1.name', 'Verified User'),
            );
        });

        it('can filter users by unverified status', function (): void {
            $verifiedUser = User::factory()->create([
                'name' => 'Verified User',
                'email_verified_at' => now(),
            ]);

            $unverifiedUser = User::factory()->create([
                'name' => 'Unverified User',
                'email_verified_at' => null,
            ]);

            $response = $this->actingAs($this->admin)->get(
                '/admin/users?verified=unverified',
            );

            expect($response)
                ->assertSuccessful()
                ->assertInertia(
                    fn ($page) => $page
                        ->component('admin/users/index')
                        ->where('filters.verified', 'unverified')
                        ->has('users.data', 1)
                        ->where('users.data.0.name', 'Unverified User'),
                );
        });

        it('can combine search, sort, and filter together', function (): void {
            $adminUser = User::factory()->create([
                'name' => 'John Admin',
                'email_verified_at' => now(),
            ]);
            $adminUser->roles()->attach($this->adminRole);

            $employeeUser = User::factory()->create([
                'name' => 'Jane Employee',
                'email_verified_at' => now(),
            ]);
            $employeeUser->roles()->attach($this->employeeRole);

            // Search for "Admin", sort by name ascending, filter by admin role
            $response = $this->actingAs($this->admin)->get(
                '/admin/users?search=Admin&sort_by=name&sort_direction=asc&role=admin',
            );

            expect($response)->assertSuccessful()->assertInertia(
                fn ($page) => $page
                    ->component('admin/users/index')
                    ->where('filters.search', 'Admin')
                    ->where('filters.sort_by', 'name')
                    ->where('filters.sort_direction', 'asc')
                    ->where('filters.role', 'admin')
                    ->has('users.data', 2) // Admin User + John Admin
                    ->where('users.data.0.name', 'Admin User')
                    ->where('users.data.1.name', 'John Admin'),
            );
        });
    });

    describe('create', function (): void {
        it(
            'displays create user form for authenticated admin',
            function (): void {
                $response = $this->actingAs($this->admin)->get(
                    '/admin/users/create',
                );

                expect($response)
                    ->assertSuccessful()
                    ->assertInertia(
                        fn ($page) => $page
                            ->component('admin/users/create')
                            ->has('roles'),
                    );
            },
        );

        it('requires authentication to access create form', function (): void {
            $response = $this->get('/admin/users/create');

            expect($response)->assertRedirect('/login');
        });

        it('requires admin role to access create form', function (): void {
            $employee = User::factory()->create();
            $employee->roles()->attach($this->employeeRole);

            $response = $this->actingAs($employee)->get('/admin/users/create');

            expect($response)->assertForbidden();
        });
    });

    describe('show', function (): void {
        it('displays user details for authenticated admin', function (): void {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
            $user->roles()->attach($this->employeeRole);

            $response = $this->actingAs($this->admin)->get(
                '/admin/users/'.$user->id,
            );

            expect($response)
                ->assertSuccessful()
                ->assertInertia(
                    fn ($page) => $page
                        ->component('admin/users/show')
                        ->has('user')
                        ->where('user.name', 'Test User')
                        ->where('user.email', 'test@example.com')
                        ->has('user.roles', 1)
                        ->where('user.roles.0.name', 'employee'),
                );
        });

        it('requires authentication to view user details', function (): void {
            $user = User::factory()->create();

            $response = $this->get('/admin/users/'.$user->id);

            expect($response)->assertRedirect('/login');
        });

        it('requires admin role to view user details', function (): void {
            $employee = User::factory()->create();
            $employee->roles()->attach($this->employeeRole);
            $user = User::factory()->create();

            $response = $this->actingAs($employee)->get(
                '/admin/users/'.$user->id,
            );

            expect($response)->assertForbidden();
        });

        it('returns 404 for non-existent user', function (): void {
            $response = $this->actingAs($this->admin)->get(
                '/admin/users/99999',
            );

            expect($response)->assertNotFound();
        });
    });

    describe('edit', function (): void {
        it(
            'displays edit user form for authenticated admin',
            function (): void {
                $user = User::factory()->create([
                    'name' => 'Edit Test User',
                    'email' => 'edit@example.com',
                ]);
                $user->roles()->attach($this->employeeRole);

                $response = $this->actingAs($this->admin)->get(
                    sprintf('/admin/users/%s/edit', $user->id),
                );

                expect($response)
                    ->assertSuccessful()
                    ->assertInertia(
                        fn ($page) => $page
                            ->component('admin/users/edit')
                            ->has('user')
                            ->where('user.name', 'Edit Test User')
                            ->where('user.email', 'edit@example.com')
                            ->has('user.roles', 1)
                            ->where('user.roles.0.name', 'employee')
                            ->has('roles'),
                    );
            },
        );

        it('requires authentication to access edit form', function (): void {
            $user = User::factory()->create();

            $response = $this->get(sprintf('/admin/users/%s/edit', $user->id));

            expect($response)->assertRedirect('/login');
        });

        it('requires admin role to access edit form', function (): void {
            $employee = User::factory()->create();
            $employee->roles()->attach($this->employeeRole);
            $user = User::factory()->create();

            $response = $this->actingAs($employee)->get(
                sprintf('/admin/users/%s/edit', $user->id),
            );

            expect($response)->assertForbidden();
        });

        it('returns 404 for non-existent user in edit', function (): void {
            $response = $this->actingAs($this->admin)->get(
                '/admin/users/99999/edit',
            );

            expect($response)->assertNotFound();
        });
    });

    describe('update', function (): void {
        it('updates user successfully', function (): void {
            $user = User::factory()->create([
                'name' => 'Original Name',
                'email' => 'original@example.com',
            ]);
            $user->roles()->attach($this->employeeRole);

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'roles' => ['admin'],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/users/'.$user->id, $updateData);

            expect($response)
                ->assertRedirect('/admin/users')
                ->assertSessionHas('status', [
                    'type' => 'success',
                    'message' => 'User updated successfully.',
                ]);

            $user->refresh();
            expect($user->name)->toBe('Updated Name');
            expect($user->email)->toBe('updated@example.com');
            expect($user->roles->pluck('name'))->toContain('admin');
            expect($user->roles->pluck('name'))->not->toContain('employee');
        });

        it('requires authentication to update users', function (): void {
            $user = User::factory()->create();

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'roles' => ['admin'],
            ];

            $response = $this->withSession(['_token' => 'test-token'])->put(
                '/admin/users/'.$user->id,
                $updateData,
            );

            expect($response)->assertRedirect('/login');
        });

        it('requires admin role to update users', function (): void {
            $employee = User::factory()->create();
            $employee->roles()->attach($this->employeeRole);
            $user = User::factory()->create();

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'roles' => ['admin'],
            ];

            $response = $this->actingAs($employee)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/users/'.$user->id, $updateData);

            expect($response)->assertForbidden();
        });

        it('validates required fields for update', function (): void {
            $user = User::factory()->create();

            $updateData = ['_token' => 'test-token'];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/users/'.$user->id, $updateData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors(['name', 'email', 'roles']);
        });

        it('validates email format for update', function (): void {
            $user = User::factory()->create();

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'invalid-email',
                'roles' => ['admin'],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/users/'.$user->id, $updateData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors('email');
        });

        it('validates unique email excluding current user', function (): void {
            $user = User::factory()->create(['email' => 'user@example.com']);
            $otherUser = User::factory()->create([
                'email' => 'other@example.com',
            ]);

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'other@example.com', // Same as other user
                'roles' => ['admin'],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/users/'.$user->id, $updateData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors('email');
        });

        it('allows updating to same email for same user', function (): void {
            $user = User::factory()->create([
                'name' => 'Original Name',
                'email' => 'user@example.com',
            ]);

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'user@example.com', // Same email
                'roles' => ['admin'],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/users/'.$user->id, $updateData);

            expect($response)->assertRedirect('/admin/users');

            $user->refresh();
            expect($user->name)->toBe('Updated Name');
            expect($user->email)->toBe('user@example.com');
        });

        it('validates roles are required for update', function (): void {
            $user = User::factory()->create();

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'roles' => [],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/users/'.$user->id, $updateData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors('roles');
        });

        it('validates roles exist for update', function (): void {
            $user = User::factory()->create();

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'roles' => ['nonexistent'],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/users/'.$user->id, $updateData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors('roles.0');
        });

        it('updates user roles correctly', function (): void {
            $user = User::factory()->create();
            $user->roles()->attach($this->employeeRole);

            $managerRole = Role::factory()->create(['name' => 'manager']);

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Role Updated User',
                'email' => 'role@example.com',
                'roles' => ['admin', 'manager'],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/users/'.$user->id, $updateData);

            expect($response)->assertRedirect('/admin/users');

            $user->refresh();
            expect($user->roles->pluck('name')->sort())->toEqual(
                collect(['admin', 'manager'])->sort(),
            );
        });

        it('returns 404 for non-existent user in update', function (): void {
            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'roles' => ['admin'],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/users/99999', $updateData);

            expect($response)->assertNotFound();
        });

        it('handles exceptions during user update', function (): void {
            $user = User::factory()->create([
                'name' => 'Original Name',
                'email' => 'original@example.com',
            ]);

            // Add a model event listener that throws an exception during update
            User::updating(function (): void {
                throw new Exception('Database error');
            });

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'roles' => ['admin'],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/users/'.$user->id, $updateData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHas('status', [
                    'type' => 'error',
                    'message' => 'Failed to update user. Please try again.',
                ]);
        });
    });

    describe('store', function (): void {
        it('creates a new user successfully', function (): void {
            $userRole = Role::factory()->create(['name' => 'user']);

            $userData = [
                '_token' => 'test-token',
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'password' => 'password123',
                'roles' => ['user'],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/users', $userData);

            expect($response)
                ->assertRedirect('/admin/users')
                ->assertSessionHas('status', [
                    'type' => 'success',
                    'message' => 'User created successfully.',
                ]);

            $this->assertDatabaseHas('users', [
                'name' => 'New User',
                'email' => 'newuser@example.com',
            ]);

            $newUser = User::query()
                ->where('email', 'newuser@example.com')
                ->first();
            expect($newUser)->not->toBeNull();
            expect($newUser->password)->not->toBeNull();
            expect($newUser->roles->pluck('name'))->toContain('user');
        });

        it('requires authentication to create users', function (): void {
            $userData = [
                '_token' => 'test-token',
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'roles' => ['user'],
            ];

            $response = $this->withSession(['_token' => 'test-token'])->post(
                '/admin/users',
                $userData,
            );

            expect($response)->assertRedirect('/login');
        });

        it('requires admin role to create users', function (): void {
            $employee = User::factory()->create();
            $employee->roles()->attach($this->employeeRole);

            $userData = [
                '_token' => 'test-token',
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'password' => 'password123',
                'roles' => ['user'],
            ];

            $response = $this->actingAs($employee)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/users', $userData);

            expect($response)->assertForbidden();
        });

        it('validates required fields', function (): void {
            $userData = ['_token' => 'test-token'];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/users', $userData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors(['name', 'email', 'roles']);
        });

        it('validates email format', function (): void {
            $userData = [
                '_token' => 'test-token',
                'name' => 'New User',
                'email' => 'invalid-email',
                'roles' => ['user'],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/users', $userData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors('email');
        });

        it('validates unique email', function (): void {
            User::factory()->create(['email' => 'existing@example.com']);

            $userData = [
                '_token' => 'test-token',
                'name' => 'New User',
                'email' => 'existing@example.com',
                'roles' => ['user'],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/users', $userData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors('email');
        });

        it('validates roles are required', function (): void {
            $userData = [
                '_token' => 'test-token',
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'roles' => [],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/users', $userData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors('roles');
        });

        it('validates roles exist', function (): void {
            $userData = [
                '_token' => 'test-token',
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'roles' => ['nonexistent'],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/users', $userData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors('roles.0');
        });

        it('assigns multiple roles correctly', function (): void {
            $userRole = Role::factory()->create(['name' => 'user']);
            $managerRole = Role::factory()->create(['name' => 'manager']);

            $userData = [
                '_token' => 'test-token',
                'name' => 'Multi Role User',
                'email' => 'multi@example.com',
                'roles' => ['user', 'manager'],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/users', $userData);

            expect($response)->assertRedirect('/admin/users');

            $newUser = User::query()
                ->where('email', 'multi@example.com')
                ->first();
            expect($newUser->roles->pluck('name')->sort())->toEqual(
                collect(['user', 'manager'])->sort(),
            );
        });

        it('handles exceptions during user creation', function (): void {
            $userRole = Role::factory()->create(['name' => 'user']);

            // Add a model event listener that throws an exception during creation
            User::creating(function (): void {
                throw new Exception('Database error');
            });

            $userData = [
                '_token' => 'test-token',
                'name' => 'Exception User',
                'email' => 'exception@example.com',
                'roles' => ['user'],
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/users', $userData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHas('status', [
                    'type' => 'error',
                    'message' => 'Failed to create user. Please try again.',
                ]);
        });
    });
});
