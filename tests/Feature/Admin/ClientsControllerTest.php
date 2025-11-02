<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Role;
use App\Models\User;

describe('Admin Clients Controller', function (): void {
    beforeEach(function (): void {
        $this->adminRole = Role::factory()->create(['name' => 'admin']);
        $this->employeeRole = Role::factory()->create(['name' => 'employee']);

        $this->admin = User::factory()->create(['name' => 'Admin User']);
        $this->admin->roles()->attach($this->adminRole);
    });

    describe('index', function (): void {
        it('displays clients list for authenticated admin', function (): void {
            $clients = Client::factory(5)->create();

            $response = $this->actingAs($this->admin)->get('/admin/clients');

            expect($response)
                ->assertSuccessful()
                ->assertInertia(
                    fn ($page) => $page
                        ->component('admin/clients/index')
                        ->has('clients.data', 5)
                        ->has('filters'),
                );
        });

        it('requires authentication to access clients list', function (): void {
            $response = $this->get('/admin/clients');

            expect($response)->assertRedirect('/login');
        });

        it('requires admin role to access clients list', function (): void {
            $employee = User::factory()->create();
            $employee->roles()->attach($this->employeeRole);

            $response = $this->actingAs($employee)->get('/admin/clients');

            expect($response)->assertForbidden();
        });

        it('can search clients by name', function (): void {
            $client1 = Client::factory()->create([
                'name' => 'TechCorp Solutions',
            ]);
            $client2 = Client::factory()->create([
                'name' => 'Global Industries',
            ]);

            $response = $this->actingAs($this->admin)->get(
                '/admin/clients?search=TechCorp',
            );

            expect($response)
                ->assertSuccessful()
                ->assertInertia(
                    fn ($page) => $page
                        ->component('admin/clients/index')
                        ->where('filters.search', 'TechCorp')
                        ->has('clients.data', 1)
                        ->where('clients.data.0.name', 'TechCorp Solutions'),
                );
        });

        it('can search clients by email', function (): void {
            $client1 = Client::factory()->create([
                'email' => 'contact@techcorp.com',
            ]);
            $client2 = Client::factory()->create([
                'email' => 'info@global.com',
            ]);

            $response = $this->actingAs($this->admin)->get(
                '/admin/clients?search=techcorp',
            );

            expect($response)
                ->assertSuccessful()
                ->assertInertia(
                    fn ($page) => $page
                        ->component('admin/clients/index')
                        ->where('filters.search', 'techcorp')
                        ->has('clients.data', 1)
                        ->where('clients.data.0.email', 'contact@techcorp.com'),
                );
        });

        it('returns paginated results', function (): void {
            Client::factory(20)->create();

            $response = $this->actingAs($this->admin)->get('/admin/clients');

            expect($response)->assertSuccessful()->assertInertia(
                fn ($page) => $page
                    ->component('admin/clients/index')
                    ->has('clients.data', 15) // Default pagination is 15
                    ->where('clients.current_page', 1)
                    ->where('clients.per_page', 15)
                    ->where('clients.total', 20),
            );
        });

        it('can sort clients by name ascending', function (): void {
            $clientZ = Client::factory()->create(['name' => 'Zeta Corp']);
            $clientA = Client::factory()->create(['name' => 'Alpha Inc']);

            $response = $this->actingAs($this->admin)->get(
                '/admin/clients?sort_by=name&sort_direction=asc',
            );

            expect($response)
                ->assertSuccessful()
                ->assertInertia(
                    fn ($page) => $page
                        ->component('admin/clients/index')
                        ->where('filters.sort_by', 'name')
                        ->where('filters.sort_direction', 'asc')
                        ->has('clients.data', 2)
                        ->where('clients.data.0.name', 'Alpha Inc'),
                );
        });

        it('can sort clients by name descending', function (): void {
            $clientZ = Client::factory()->create(['name' => 'Zeta Corp']);
            $clientA = Client::factory()->create(['name' => 'Alpha Inc']);

            $response = $this->actingAs($this->admin)->get(
                '/admin/clients?sort_by=name&sort_direction=desc',
            );

            expect($response)
                ->assertSuccessful()
                ->assertInertia(
                    fn ($page) => $page
                        ->component('admin/clients/index')
                        ->where('filters.sort_by', 'name')
                        ->where('filters.sort_direction', 'desc')
                        ->has('clients.data', 2)
                        ->where('clients.data.0.name', 'Zeta Corp'),
                );
        });

        it('can filter clients by status', function (): void {
            $activeClient = Client::factory()->create(['status' => 'active']);
            $inactiveClient = Client::factory()->create([
                'status' => 'inactive',
            ]);

            $response = $this->actingAs($this->admin)->get(
                '/admin/clients?status=active',
            );

            expect($response)
                ->assertSuccessful()
                ->assertInertia(
                    fn ($page) => $page
                        ->component('admin/clients/index')
                        ->where('filters.status', 'active')
                        ->has('clients.data', 1)
                        ->where('clients.data.0.status', 'active'),
                );
        });

        it('can combine search, sort, and filter together', function (): void {
            $activeClient = Client::factory()->create([
                'name' => 'Tech Solutions',
                'status' => 'active',
            ]);
            $inactiveClient = Client::factory()->create([
                'name' => 'Global Tech',
                'status' => 'inactive',
            ]);

            // Search for "Tech", sort by name ascending, filter by active status
            $response = $this->actingAs($this->admin)->get(
                '/admin/clients?search=Tech&sort_by=name&sort_direction=asc&status=active',
            );

            expect($response)
                ->assertSuccessful()
                ->assertInertia(
                    fn ($page) => $page
                        ->component('admin/clients/index')
                        ->where('filters.search', 'Tech')
                        ->where('filters.sort_by', 'name')
                        ->where('filters.sort_direction', 'asc')
                        ->where('filters.status', 'active')
                        ->has('clients.data', 1)
                        ->where('clients.data.0.name', 'Tech Solutions'),
                );
        });
    });

    describe('create', function (): void {
        it(
            'displays create client form for authenticated admin',
            function (): void {
                $response = $this->actingAs($this->admin)->get(
                    '/admin/clients/create',
                );

                expect($response)
                    ->assertSuccessful()
                    ->assertInertia(
                        fn ($page) => $page->component('admin/clients/create'),
                    );
            },
        );

        it('requires authentication to access create form', function (): void {
            $response = $this->get('/admin/clients/create');

            expect($response)->assertRedirect('/login');
        });

        it('requires admin role to access create form', function (): void {
            $employee = User::factory()->create();
            $employee->roles()->attach($this->employeeRole);

            $response = $this->actingAs($employee)->get(
                '/admin/clients/create',
            );

            expect($response)->assertForbidden();
        });
    });

    describe('show', function (): void {
        it(
            'displays client details for authenticated admin',
            function (): void {
                $client = Client::factory()->create([
                    'name' => 'Test Client',
                    'email' => 'test@client.com',
                ]);

                $response = $this->actingAs($this->admin)->get(
                    '/admin/clients/'.$client->id,
                );

                expect($response)
                    ->assertSuccessful()
                    ->assertInertia(
                        fn ($page) => $page
                            ->component('admin/clients/show')
                            ->has('client')
                            ->where('client.name', 'Test Client')
                            ->where('client.email', 'test@client.com'),
                    );
            },
        );

        it('requires authentication to view client details', function (): void {
            $client = Client::factory()->create();

            $response = $this->get('/admin/clients/'.$client->id);

            expect($response)->assertRedirect('/login');
        });

        it('requires admin role to view client details', function (): void {
            $employee = User::factory()->create();
            $employee->roles()->attach($this->employeeRole);
            $client = Client::factory()->create();

            $response = $this->actingAs($employee)->get(
                '/admin/clients/'.$client->id,
            );

            expect($response)->assertForbidden();
        });

        it('returns 404 for non-existent client', function (): void {
            $response = $this->actingAs($this->admin)->get(
                '/admin/clients/99999',
            );

            expect($response)->assertNotFound();
        });
    });

    describe('edit', function (): void {
        it(
            'displays edit client form for authenticated admin',
            function (): void {
                $client = Client::factory()->create([
                    'name' => 'Edit Test Client',
                    'email' => 'edit@client.com',
                ]);

                $response = $this->actingAs($this->admin)->get(
                    sprintf('/admin/clients/%s/edit', $client->id),
                );

                expect($response)
                    ->assertSuccessful()
                    ->assertInertia(
                        fn ($page) => $page
                            ->component('admin/clients/edit')
                            ->has('client')
                            ->where('client.name', 'Edit Test Client')
                            ->where('client.email', 'edit@client.com'),
                    );
            },
        );

        it('requires authentication to access edit form', function (): void {
            $client = Client::factory()->create();

            $response = $this->get(
                sprintf('/admin/clients/%s/edit', $client->id),
            );

            expect($response)->assertRedirect('/login');
        });

        it('requires admin role to access edit form', function (): void {
            $employee = User::factory()->create();
            $employee->roles()->attach($this->employeeRole);
            $client = Client::factory()->create();

            $response = $this->actingAs($employee)->get(
                sprintf('/admin/clients/%s/edit', $client->id),
            );

            expect($response)->assertForbidden();
        });

        it('returns 404 for non-existent client in edit', function (): void {
            $response = $this->actingAs($this->admin)->get(
                '/admin/clients/99999/edit',
            );

            expect($response)->assertNotFound();
        });
    });

    describe('update', function (): void {
        it('updates client successfully', function (): void {
            $client = Client::factory()->create([
                'name' => 'Original Name',
                'email' => 'original@client.com',
                'status' => 'prospect',
            ]);

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'updated@client.com',
                'phone' => '+1234567890',
                'address' => '123 Updated St',
                'status' => 'active',
                'industry' => 'Technology',
                'contact_person' => 'John Updated',
                'website' => 'https://updated.com',
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/clients/'.$client->id, $updateData);

            expect($response)
                ->assertRedirect('/admin/clients')
                ->assertSessionHas('status', [
                    'type' => 'success',
                    'message' => 'Client updated successfully.',
                ]);

            $client->refresh();
            expect($client->name)->toBe('Updated Name');
            expect($client->email)->toBe('updated@client.com');
            expect($client->phone)->toBe('+1234567890');
            expect($client->status)->toBe('active');
        });

        it('requires authentication to update clients', function (): void {
            $client = Client::factory()->create();

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'updated@client.com',
                'status' => 'active',
            ];

            $response = $this->withSession(['_token' => 'test-token'])->put(
                '/admin/clients/'.$client->id,
                $updateData,
            );

            expect($response)->assertRedirect('/login');
        });

        it('requires admin role to update clients', function (): void {
            $employee = User::factory()->create();
            $employee->roles()->attach($this->employeeRole);
            $client = Client::factory()->create();

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'updated@client.com',
                'status' => 'active',
            ];

            $response = $this->actingAs($employee)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/clients/'.$client->id, $updateData);

            expect($response)->assertForbidden();
        });

        it('validates required fields for update', function (): void {
            $client = Client::factory()->create();

            $updateData = ['_token' => 'test-token'];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/clients/'.$client->id, $updateData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors(['name', 'email', 'status']);
        });

        it('validates email format for update', function (): void {
            $client = Client::factory()->create();

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'invalid-email',
                'status' => 'active',
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/clients/'.$client->id, $updateData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors('email');
        });

        it(
            'validates unique email excluding current client',
            function (): void {
                $client = Client::factory()->create([
                    'email' => 'client@example.com',
                ]);
                $otherClient = Client::factory()->create([
                    'email' => 'other@example.com',
                ]);

                $updateData = [
                    '_token' => 'test-token',
                    'name' => 'Updated Name',
                    'email' => 'other@example.com', // Same as other client
                    'status' => 'active',
                ];

                $response = $this->actingAs($this->admin)
                    ->withSession(['_token' => 'test-token'])
                    ->put('/admin/clients/'.$client->id, $updateData);

                expect($response)
                    ->assertRedirect()
                    ->assertSessionHasErrors('email');
            },
        );

        it('allows updating to same email for same client', function (): void {
            $client = Client::factory()->create([
                'name' => 'Original Name',
                'email' => 'client@example.com',
            ]);

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'client@example.com', // Same email
                'status' => 'active',
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/clients/'.$client->id, $updateData);

            expect($response)->assertRedirect('/admin/clients');

            $client->refresh();
            expect($client->name)->toBe('Updated Name');
            expect($client->email)->toBe('client@example.com');
        });

        it('validates status enum values', function (): void {
            $client = Client::factory()->create();

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'updated@client.com',
                'status' => 'invalid_status',
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/clients/'.$client->id, $updateData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors('status');
        });

        it('returns 404 for non-existent client in update', function (): void {
            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'updated@client.com',
                'status' => 'active',
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/clients/99999', $updateData);

            expect($response)->assertNotFound();
        });

        it('handles exceptions during client update', function (): void {
            $client = Client::factory()->create([
                'name' => 'Original Name',
                'email' => 'original@client.com',
            ]);

            // Add a model event listener that throws an exception during update
            Client::updating(function (): void {
                throw new Exception('Database error');
            });

            $updateData = [
                '_token' => 'test-token',
                'name' => 'Updated Name',
                'email' => 'updated@client.com',
                'status' => 'active',
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->put('/admin/clients/'.$client->id, $updateData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHas('status', [
                    'type' => 'error',
                    'message' => 'Failed to update client. Please try again.',
                ]);
        });
    });

    describe('store', function (): void {
        it('creates a new client successfully', function (): void {
            $clientData = [
                '_token' => 'test-token',
                'name' => 'New Client Corp',
                'email' => 'contact@newclient.com',
                'phone' => '+1234567890',
                'address' => '123 Business St, City, State 12345',
                'status' => 'prospect',
                'industry' => 'Technology',
                'contact_person' => 'Jane Doe',
                'website' => 'https://newclient.com',
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/clients', $clientData);

            expect($response)
                ->assertRedirect('/admin/clients')
                ->assertSessionHas('status', [
                    'type' => 'success',
                    'message' => 'Client created successfully.',
                ]);

            $this->assertDatabaseHas('clients', [
                'name' => 'New Client Corp',
                'email' => 'contact@newclient.com',
                'phone' => '+1234567890',
                'status' => 'prospect',
                'industry' => 'Technology',
                'contact_person' => 'Jane Doe',
                'website' => 'https://newclient.com',
            ]);
        });

        it('requires authentication to create clients', function (): void {
            $clientData = [
                '_token' => 'test-token',
                'name' => 'New Client Corp',
                'email' => 'contact@newclient.com',
                'status' => 'prospect',
            ];

            $response = $this->withSession(['_token' => 'test-token'])->post(
                '/admin/clients',
                $clientData,
            );

            expect($response)->assertRedirect('/login');
        });

        it('requires admin role to create clients', function (): void {
            $employee = User::factory()->create();
            $employee->roles()->attach($this->employeeRole);

            $clientData = [
                '_token' => 'test-token',
                'name' => 'New Client Corp',
                'email' => 'contact@newclient.com',
                'status' => 'prospect',
            ];

            $response = $this->actingAs($employee)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/clients', $clientData);

            expect($response)->assertForbidden();
        });

        it('validates required fields', function (): void {
            $clientData = ['_token' => 'test-token'];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/clients', $clientData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors(['name', 'email', 'status']);
        });

        it('validates email format', function (): void {
            $clientData = [
                '_token' => 'test-token',
                'name' => 'New Client Corp',
                'email' => 'invalid-email',
                'status' => 'prospect',
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/clients', $clientData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors('email');
        });

        it('validates unique email', function (): void {
            Client::factory()->create(['email' => 'existing@client.com']);

            $clientData = [
                '_token' => 'test-token',
                'name' => 'New Client Corp',
                'email' => 'existing@client.com',
                'status' => 'prospect',
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/clients', $clientData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors('email');
        });

        it('validates status enum values', function (): void {
            $clientData = [
                '_token' => 'test-token',
                'name' => 'New Client Corp',
                'email' => 'contact@newclient.com',
                'status' => 'invalid_status',
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/clients', $clientData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors('status');
        });

        it('validates website URL format', function (): void {
            $clientData = [
                '_token' => 'test-token',
                'name' => 'New Client Corp',
                'email' => 'contact@newclient.com',
                'status' => 'prospect',
                'website' => 'not-a-valid-url',
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/clients', $clientData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHasErrors('website');
        });

        it('handles exceptions during client creation', function (): void {
            // Add a model event listener that throws an exception during creation
            Client::creating(function (): void {
                throw new Exception('Database error');
            });

            $clientData = [
                '_token' => 'test-token',
                'name' => 'New Client Corp',
                'email' => 'contact@newclient.com',
                'status' => 'prospect',
            ];

            $response = $this->actingAs($this->admin)
                ->withSession(['_token' => 'test-token'])
                ->post('/admin/clients', $clientData);

            expect($response)
                ->assertRedirect()
                ->assertSessionHas('status', [
                    'type' => 'error',
                    'message' => 'Failed to create client. Please try again.',
                ]);
        });
    });
});
