<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;

describe('Admin Users Controller', function (): void {
    beforeEach(function (): void {
        $this->adminRole = Role::factory()->create(['name' => 'admin']);
        $this->employeeRole = Role::factory()->create(['name' => 'employee']);

        $this->admin = User::factory()->create();
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
                    ->where('users.data.0.name', 'Alice First'),
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
    });
});
