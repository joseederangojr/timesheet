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
                    ->component('admin/Users/Index')
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
                        ->component('admin/Users/Index')
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
                        ->component('admin/Users/Index')
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
                    ->component('admin/Users/Index')
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
                        ->component('admin/Users/Index')
                        ->has(
                            'users.data.1.roles.0',
                            fn ($roleData) => $roleData
                                ->where('name', 'manager')
                                ->etc(),
                        ),
                );
        });
    });
});
