<?php

declare(strict_types=1);

use App\Actions\UpdateEmployment;
use App\Data\UpdateEmploymentData;
use App\Enums\EmploymentStatus;
use App\Models\Client;
use App\Models\Employment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->action = app(UpdateEmployment::class);
});

it('updates employment successfully', function (): void {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $employment = Employment::factory()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'position' => 'Junior Developer',
        'hire_date' => '2023-01-01',
        'status' => EmploymentStatus::ACTIVE,
        'salary' => '50000.00',
        'work_location' => 'Office',
        'effective_date' => '2023-01-01',
        'end_date' => null,
    ]);

    $newUser = User::factory()->create();
    $newClient = Client::factory()->create();

    $data = new UpdateEmploymentData(
        employment: $employment,
        user: $newUser,
        client: $newClient,
        position: 'Senior Developer',
        hireDate: '2023-06-01',
        status: EmploymentStatus::ACTIVE,
        salary: '90000.00',
        workLocation: 'Remote',
        effectiveDate: '2023-06-01',
        endDate: '2025-06-01',
    );

    $updatedEmployment = $this->action->handle($data);

    expect($updatedEmployment)->toBeInstanceOf(Employment::class);
    expect($updatedEmployment->id)->toBe($employment->id);
    expect($updatedEmployment->user_id)->toBe($newUser->id);
    expect($updatedEmployment->client_id)->toBe($newClient->id);
    expect($updatedEmployment->position)->toBe('Senior Developer');
    expect($updatedEmployment->hire_date->format('Y-m-d'))->toBe('2023-06-01');
    expect($updatedEmployment->status)->toBe(EmploymentStatus::ACTIVE);
    expect($updatedEmployment->salary)->toBe('90000.00');
    expect($updatedEmployment->work_location)->toBe('Remote');
    expect($updatedEmployment->effective_date->format('Y-m-d'))->toBe(
        '2023-06-01',
    );
    expect($updatedEmployment->end_date->format('Y-m-d'))->toBe('2025-06-01');

    $this->assertDatabaseHas('employments', [
        'id' => $employment->id,
        'user_id' => $newUser->id,
        'client_id' => $newClient->id,
        'position' => 'Senior Developer',
        'hire_date' => '2023-06-01 00:00:00',
        'status' => EmploymentStatus::ACTIVE->value,
        'salary' => '90000.00',
        'work_location' => 'Remote',
        'effective_date' => '2023-06-01 00:00:00',
        'end_date' => '2025-06-01 00:00:00',
    ]);
});

it('updates employment without client', function (): void {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $employment = Employment::factory()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'position' => 'Designer',
        'status' => EmploymentStatus::ACTIVE,
    ]);

    $newUser = User::factory()->create();

    $data = new UpdateEmploymentData(
        employment: $employment,
        user: $newUser,
        client: null,
        position: 'Senior Designer',
        hireDate: '2023-03-01',
        status: EmploymentStatus::INACTIVE,
        salary: '70000.00',
        workLocation: 'Hybrid',
        effectiveDate: '2023-03-01',
        endDate: null,
    );

    $updatedEmployment = $this->action->handle($data);

    expect($updatedEmployment->client_id)->toBeNull();
    expect($updatedEmployment->position)->toBe('Senior Designer');
    expect($updatedEmployment->status)->toBe(EmploymentStatus::INACTIVE);

    $this->assertDatabaseHas('employments', [
        'id' => $employment->id,
        'client_id' => null,
        'position' => 'Senior Designer',
        'status' => EmploymentStatus::INACTIVE->value,
    ]);
});

it('updates employment status', function (): void {
    $user = User::factory()->create();
    $employment = Employment::factory()->create([
        'user_id' => $user->id,
        'status' => EmploymentStatus::ACTIVE,
    ]);

    $data = new UpdateEmploymentData(
        employment: $employment,
        user: $user,
        client: null,
        position: $employment->position,
        hireDate: $employment->hire_date->format('Y-m-d'),
        status: EmploymentStatus::INACTIVE,
        salary: $employment->salary,
        workLocation: $employment->work_location,
        effectiveDate: $employment->effective_date->format('Y-m-d'),
        endDate: $employment->end_date?->format('Y-m-d'),
    );

    $updatedEmployment = $this->action->handle($data);

    expect($updatedEmployment->status)->toBe(EmploymentStatus::INACTIVE);
    $this->assertDatabaseHas('employments', [
        'id' => $employment->id,
        'status' => EmploymentStatus::INACTIVE->value,
        'hire_date' => $employment->hire_date->format('Y-m-d H:i:s'),
        'effective_date' => $employment->effective_date->format('Y-m-d H:i:s'),
    ]);
});
