<?php

declare(strict_types=1);

use App\Enums\EmploymentStatus;
use App\Models\Client;
use App\Models\Employment;
use App\Models\User;
use Carbon\CarbonImmutable;

test('employment belongs to user', function (): void {
    $employment = Employment::factory()->create();

    expect($employment->user)->toBeInstanceOf(User::class);
    expect($employment->user->id)->toBe($employment->user_id);
});

test('employment belongs to client', function (): void {
    $employment = Employment::factory()->create();

    expect($employment->client)->toBeInstanceOf(Client::class);
    expect($employment->client->id)->toBe($employment->client_id);
});

test('employment casts status to enum', function (): void {
    $employment = Employment::factory()->create(['status' => 'active']);

    expect($employment->status)->toBeInstanceOf(EmploymentStatus::class);
    expect($employment->status->value)->toBe('active');
    expect($employment->status->label())->toBe('Active');
});

test('employment casts dates correctly', function (): void {
    $employment = Employment::factory()->create();

    expect($employment->hire_date)->toBeInstanceOf(CarbonImmutable::class);
    expect($employment->effective_date)->toBeInstanceOf(CarbonImmutable::class);

    if ($employment->end_date) {
        expect($employment->end_date)->toBeInstanceOf(CarbonImmutable::class);
    }
});

test('employment casts salary as decimal', function (): void {
    $salary = '50000.50';
    $employment = Employment::factory()->create(['salary' => $salary]);

    expect($employment->salary)->toBe($salary);
    expect(is_string($employment->salary))->toBeTrue();
});

test('employment uses correct table name', function (): void {
    $employment = new Employment();

    expect($employment->getTable())->toBe('employments');
});

test('employment has correct fillable attributes', function (): void {
    $employment = new Employment();
    $expectedFillable = [
        'user_id',
        'client_id',
        'position',
        'hire_date',
        'status',
        'salary',
        'work_location',
        'effective_date',
        'end_date',
    ];

    expect($employment->getFillable())->toEqual($expectedFillable);
});

test('employment can be created with valid data', function (): void {
    $user = User::factory()->create();
    $client = Client::factory()->create();

    $data = [
        'user_id' => $user->id,
        'client_id' => $client->id,
        'position' => 'Software Developer',
        'hire_date' => '2023-01-15',
        'status' => 'active',
        'salary' => '75000.00',
        'work_location' => 'New York',
        'effective_date' => '2023-01-15',
    ];

    $employment = Employment::query()->create($data);

    expect($employment)->toBeInstanceOf(Employment::class);
    expect($employment->position)->toBe('Software Developer');
    expect($employment->status)->toBeInstanceOf(EmploymentStatus::class);
    expect($employment->status->value)->toBe('active');
    expect($employment->salary)->toBe('75000.00');
});
