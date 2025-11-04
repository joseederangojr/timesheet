<?php

declare(strict_types=1);

use App\Actions\CreateEmployment;
use App\Data\CreateEmploymentData;
use App\Enums\EmploymentStatus;
use App\Models\Client;
use App\Models\Employment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->action = app(CreateEmployment::class);
});

it('creates employment successfully', function (): void {
    $user = User::factory()->create();
    $client = Client::factory()->create();

    $data = new CreateEmploymentData(
        user: $user,
        client: $client,
        position: 'Software Developer',
        hireDate: '2024-01-15',
        status: EmploymentStatus::ACTIVE,
        salary: '75000.00',
        workLocation: 'Remote',
        effectiveDate: '2024-01-15',
        endDate: null,
    );

    $employment = $this->action->handle($data);

    expect($employment)->toBeInstanceOf(Employment::class);
    expect($employment->user_id)->toBe($user->id);
    expect($employment->client_id)->toBe($client->id);
    expect($employment->position)->toBe('Software Developer');
    expect($employment->hire_date->format('Y-m-d'))->toBe('2024-01-15');
    expect($employment->status)->toBe(EmploymentStatus::ACTIVE);
    expect($employment->salary)->toBe('75000.00');
    expect($employment->work_location)->toBe('Remote');
    expect($employment->effective_date->format('Y-m-d'))->toBe('2024-01-15');
    expect($employment->end_date)->toBeNull();

    $this->assertDatabaseHas('employments', [
        'user_id' => $user->id,
        'client_id' => $client->id,
        'position' => 'Software Developer',
        'hire_date' => '2024-01-15 00:00:00',
        'status' => EmploymentStatus::ACTIVE->value,
        'salary' => '75000.00',
        'work_location' => 'Remote',
        'effective_date' => '2024-01-15 00:00:00',
        'end_date' => null,
    ]);
});

it('creates employment without client', function (): void {
    $user = User::factory()->create();

    $data = new CreateEmploymentData(
        user: $user,
        client: null,
        position: 'Project Manager',
        hireDate: '2024-02-01',
        status: EmploymentStatus::ACTIVE,
        salary: '85000.00',
        workLocation: 'Office',
        effectiveDate: '2024-02-01',
        endDate: '2025-02-01',
    );

    $employment = $this->action->handle($data);

    expect($employment)->toBeInstanceOf(Employment::class);
    expect($employment->user_id)->toBe($user->id);
    expect($employment->client_id)->toBeNull();
    expect($employment->position)->toBe('Project Manager');
    expect($employment->hire_date->format('Y-m-d'))->toBe('2024-02-01');
    expect($employment->status)->toBe(EmploymentStatus::ACTIVE);
    expect($employment->salary)->toBe('85000.00');
    expect($employment->work_location)->toBe('Office');
    expect($employment->effective_date->format('Y-m-d'))->toBe('2024-02-01');
    expect($employment->end_date->format('Y-m-d'))->toBe('2025-02-01');

    $this->assertDatabaseHas('employments', [
        'user_id' => $user->id,
        'client_id' => null,
        'position' => 'Project Manager',
        'hire_date' => '2024-02-01 00:00:00',
        'status' => EmploymentStatus::ACTIVE->value,
        'salary' => '85000.00',
        'work_location' => 'Office',
        'effective_date' => '2024-02-01 00:00:00',
        'end_date' => '2025-02-01 00:00:00',
    ]);
});

it('creates employment with different statuses', function (): void {
    $user = User::factory()->create();

    $data = new CreateEmploymentData(
        user: $user,
        client: null,
        position: 'Consultant',
        hireDate: '2024-03-01',
        status: EmploymentStatus::TERMINATED,
        salary: null,
        workLocation: null,
        effectiveDate: '2024-03-01',
        endDate: null,
    );

    $employment = $this->action->handle($data);

    expect($employment->status)->toBe(EmploymentStatus::TERMINATED);
    $this->assertDatabaseHas('employments', [
        'user_id' => $user->id,
        'status' => EmploymentStatus::TERMINATED->value,
        'hire_date' => '2024-03-01 00:00:00',
        'effective_date' => '2024-03-01 00:00:00',
    ]);
});