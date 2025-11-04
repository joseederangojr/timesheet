<?php

declare(strict_types=1);

use App\Enums\EmploymentStatus;

it('has active status', function (): void {
    expect(EmploymentStatus::ACTIVE)->toBeInstanceOf(EmploymentStatus::class);
    expect(EmploymentStatus::ACTIVE->value)->toBe('active');
});

it('has inactive status', function (): void {
    expect(EmploymentStatus::INACTIVE)->toBeInstanceOf(EmploymentStatus::class);
    expect(EmploymentStatus::INACTIVE->value)->toBe('inactive');
});

it('has terminated status', function (): void {
    expect(EmploymentStatus::TERMINATED)->toBeInstanceOf(EmploymentStatus::class);
    expect(EmploymentStatus::TERMINATED->value)->toBe('terminated');
});

it('returns correct label for active status', function (): void {
    expect(EmploymentStatus::ACTIVE->label())->toBe('Active');
});

it('returns correct label for inactive status', function (): void {
    expect(EmploymentStatus::INACTIVE->label())->toBe('Inactive');
});

it('returns correct label for terminated status', function (): void {
    expect(EmploymentStatus::TERMINATED->label())->toBe('Terminated');
});

it('can be created from string values', function (): void {
    expect(EmploymentStatus::from('active'))->toBe(EmploymentStatus::ACTIVE);
    expect(EmploymentStatus::from('inactive'))->toBe(EmploymentStatus::INACTIVE);
    expect(EmploymentStatus::from('terminated'))->toBe(EmploymentStatus::TERMINATED);
});

it('can be used in match expressions', function (): void {
    $status = EmploymentStatus::ACTIVE;

    $result = match ($status) {
        EmploymentStatus::ACTIVE => 'is active',
        EmploymentStatus::INACTIVE => 'is inactive',
        EmploymentStatus::TERMINATED => 'is terminated',
    };

    expect($result)->toBe('is active');
});

it('can be iterated over', function (): void {
    $cases = EmploymentStatus::cases();

    expect($cases)->toHaveCount(3);
    expect($cases)->toContain(EmploymentStatus::ACTIVE);
    expect($cases)->toContain(EmploymentStatus::INACTIVE);
    expect($cases)->toContain(EmploymentStatus::TERMINATED);
});
