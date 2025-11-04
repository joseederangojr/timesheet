<?php

declare(strict_types=1);

use App\Enums\EmploymentStatus;
use App\Models\Client;
use App\Models\Employment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

describe('Admin Employments Controller', function (): void {
    beforeEach(function (): void {
        $this->adminRole = Role::factory()->create(['name' => 'admin']);
        $this->employeeRole = Role::factory()->create(['name' => 'employee']);

        $this->admin = User::factory()->create(['name' => 'Admin User']);
        $this->admin->roles()->attach($this->adminRole);

        $this->withoutMiddleware([
            Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            Illuminate\Auth\Middleware\Authenticate::class,
        ]);

        $this->startSession();
    });

    it('displays employments index page', function (): void {
        $employments = Employment::factory()->count(3)->create();

        $employments = Employment::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.employments.index'));

        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('admin/employments/index')
            ->has('employments')
            ->has('filters')
            ->has('clients')
        );
    });

    it('displays employments create page', function (): void {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.employments.create'));

        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('admin/employments/create')
            ->has('clients')
            ->has('users')
        );
    });

    it('stores new employment successfully', function (): void {
        $this->withoutMiddleware('web');

        $user = User::factory()->create();
        $client = Client::factory()->create();

        $data = [
            'user_id' => $user->id,
            'client_id' => $client->id,
            'position' => 'Software Developer',
            'hire_date' => '2024-01-01',
            'status' => 'active',
            'salary' => '75000.00',
            'work_location' => 'Remote',
            'effective_date' => '2024-01-01',
            'end_date' => null,
        ];

        $response = $this->actingAs($this->admin)
            ->withSession(['_token' => 'test-token'])
            ->post(route('admin.employments.store'), array_merge($data, ['_token' => 'test-token']));

        expect($response)
            ->assertRedirect(route('admin.employments.index'))
            ->assertSessionHas('status.type', 'success');

        $this->assertDatabaseHas('employments', [
            'user_id' => $user->id,
            'client_id' => $client->id,
            'position' => 'Software Developer',
            'status' => 'active',
            'salary' => 75000.00,
        ]);
    });

    it('displays employment show page', function (): void {
        $employment = Employment::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.employments.show', $employment));

        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('admin/employments/show')
            ->has('employment')
            ->where('employment.id', $employment->id)
        );
    });

    it('displays employment edit page', function (): void {
        $employment = Employment::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.employments.edit', $employment));

        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('admin/employments/edit')
            ->has('employment')
            ->has('clients')
            ->has('users')
            ->where('employment.id', $employment->id)
        );
    });

    it('updates employment successfully', function (): void {
        $employment = Employment::factory()->create();
        $newUser = User::factory()->create();
        $newClient = Client::factory()->create();

        $data = [
            '_token' => 'test-token',
            'user_id' => $newUser->id,
            'client_id' => $newClient->id,
            'position' => 'Senior Software Developer',
            'hire_date' => '2024-02-01',
            'status' => 'active',
            'salary' => '85000.00',
            'work_location' => 'Office',
            'effective_date' => '2024-02-01',
            'end_date' => null,
        ];

        $response = $this->actingAs($this->admin)
            ->withSession(['_token' => 'test-token'])
            ->put(route('admin.employments.update', $employment), $data);

        expect($response)
            ->assertRedirect(route('admin.employments.index'))
            ->assertSessionHas('status.type', 'success');

        $employment->refresh();

        expect($employment->user_id)->toBe($newUser->id);
        expect($employment->client_id)->toBe($newClient->id);
        expect($employment->position)->toBe('Senior Software Developer');
        expect($employment->status)->toBe(EmploymentStatus::ACTIVE);
        expect($employment->salary)->toBe('85000.00');
    });

    it('fails to update employment with invalid data', function (): void {
        $this->withoutMiddleware('web');

        $employment = Employment::factory()->create();

        $data = [
            '_token' => 'test-token',
            'user_id' => 999, // Non-existent user
            'client_id' => 999, // Non-existent client
            'position' => '',
            'hire_date' => 'invalid-date',
            'status' => 'invalid-status',
        ];

        $response = $this->actingAs($this->admin)
            ->withSession(['_token' => 'test-token'])
            ->putJson(route('admin.employments.update', $employment), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_id', 'client_id', 'position', 'hire_date', 'status', 'effective_date']);

        $employment->refresh();

        expect($employment->position)->not->toBe('');
    });

    it('deletes employment successfully', function (): void {
        $this->withoutMiddleware('web');

        $employment = Employment::factory()->create();

        $response = $this->actingAs($this->admin)
            ->withSession(['_token' => 'test-token'])
            ->deleteJson(route('admin.employments.destroy', $employment), ['_token' => 'test-token']);

        $response->assertRedirect(route('admin.employments.index'))
            ->assertSessionHas('status.type', 'success');

        $this->assertDatabaseMissing('employments', [
            'id' => $employment->id,
        ]);
    });
});
