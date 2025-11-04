<?php

declare(strict_types=1);

use App\Models\Employment;
use App\Models\User;
use App\Policies\EmploymentPolicy;
use App\Queries\CheckUserIsAdminQuery;
use App\Queries\CheckUserIsEmployeeQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed();
});

it('allows admin users to view any employments', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->viewAny($adminUser))->toBeTrue();
});

it('allows employee users to view any employments', function (): void {
    $employeeUser = User::factory()->create();
    $employeeUser->roles()->attach(2); // Employee role

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->viewAny($employeeUser))->toBeTrue();
});

it(
    'denies non-admin and non-employee users to view any employments',
    function (): void {
        $regularUser = User::factory()->create();

        $adminQuery = app(CheckUserIsAdminQuery::class);
        $employeeQuery = app(CheckUserIsEmployeeQuery::class);
        $policy = new EmploymentPolicy(
            checkUserIsAdminQuery: $adminQuery,
            checkUserIsEmployeeQuery: $employeeQuery,
        );
        expect($policy->viewAny($regularUser))->toBeFalse();
    },
);

it('allows admin users to view specific employments', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role
    $employment = Employment::factory()->create();

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->view($adminUser, $employment))->toBeTrue();
});

it('allows employee users to view their own employments', function (): void {
    $employeeUser = User::factory()->create();
    $employeeUser->roles()->attach(2); // Employee role
    $employment = Employment::factory()->create([
        'user_id' => $employeeUser->id,
    ]);

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->view($employeeUser, $employment))->toBeTrue();
});

it('denies employee users to view other employments', function (): void {
    $employeeUser = User::factory()->create();
    $employeeUser->roles()->attach(2); // Employee role
    $otherUser = User::factory()->create();
    $employment = Employment::factory()->create(['user_id' => $otherUser->id]);

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->view($employeeUser, $employment))->toBeFalse();
});

it(
    'denies non-admin and non-employee users to view specific employments',
    function (): void {
        $regularUser = User::factory()->create();
        $employment = Employment::factory()->create();

        $adminQuery = app(CheckUserIsAdminQuery::class);
        $employeeQuery = app(CheckUserIsEmployeeQuery::class);
        $policy = new EmploymentPolicy(
            checkUserIsAdminQuery: $adminQuery,
            checkUserIsEmployeeQuery: $employeeQuery,
        );
        expect($policy->view($regularUser, $employment))->toBeFalse();
    },
);

it('allows admin users to create employments', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->create($adminUser))->toBeTrue();
});

it('denies non-admin users to create employments', function (): void {
    $regularUser = User::factory()->create();

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->create($regularUser))->toBeFalse();
});

it('denies employee users to create employments', function (): void {
    $employeeUser = User::factory()->create();
    $employeeUser->roles()->attach(2); // Employee role

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->create($employeeUser))->toBeFalse();
});

it('allows admin users to update employments', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role
    $employment = Employment::factory()->create();

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->update($adminUser, $employment))->toBeTrue();
});

it('denies non-admin users to update employments', function (): void {
    $regularUser = User::factory()->create();
    $employment = Employment::factory()->create();

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->update($regularUser, $employment))->toBeFalse();
});

it('denies employee users to update employments', function (): void {
    $employeeUser = User::factory()->create();
    $employeeUser->roles()->attach(2); // Employee role
    $employment = Employment::factory()->create([
        'user_id' => $employeeUser->id,
    ]);

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->update($employeeUser, $employment))->toBeFalse();
});

it('allows admin users to delete employments', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role
    $employment = Employment::factory()->create();

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->delete($adminUser, $employment))->toBeTrue();
});

it('denies non-admin users to delete employments', function (): void {
    $regularUser = User::factory()->create();
    $employment = Employment::factory()->create();

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->delete($regularUser, $employment))->toBeFalse();
});

it('denies employee users to delete employments', function (): void {
    $employeeUser = User::factory()->create();
    $employeeUser->roles()->attach(2); // Employee role
    $employment = Employment::factory()->create([
        'user_id' => $employeeUser->id,
    ]);

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->delete($employeeUser, $employment))->toBeFalse();
});

it('allows admin users to restore employments', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role
    $employment = Employment::factory()->create();

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->restore($adminUser, $employment))->toBeTrue();
});

it('denies non-admin users to restore employments', function (): void {
    $regularUser = User::factory()->create();
    $employment = Employment::factory()->create();

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->restore($regularUser, $employment))->toBeFalse();
});

it('denies employee users to restore employments', function (): void {
    $employeeUser = User::factory()->create();
    $employeeUser->roles()->attach(2); // Employee role
    $employment = Employment::factory()->create([
        'user_id' => $employeeUser->id,
    ]);

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->restore($employeeUser, $employment))->toBeFalse();
});

it('allows admin users to force delete employments', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role
    $employment = Employment::factory()->create();

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->forceDelete($adminUser, $employment))->toBeTrue();
});

it('denies non-admin users to force delete employments', function (): void {
    $regularUser = User::factory()->create();
    $employment = Employment::factory()->create();

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->forceDelete($regularUser, $employment))->toBeFalse();
});

it('denies employee users to force delete employments', function (): void {
    $employeeUser = User::factory()->create();
    $employeeUser->roles()->attach(2); // Employee role
    $employment = Employment::factory()->create([
        'user_id' => $employeeUser->id,
    ]);

    $adminQuery = app(CheckUserIsAdminQuery::class);
    $employeeQuery = app(CheckUserIsEmployeeQuery::class);
    $policy = new EmploymentPolicy(
        checkUserIsAdminQuery: $adminQuery,
        checkUserIsEmployeeQuery: $employeeQuery,
    );
    expect($policy->forceDelete($employeeUser, $employment))->toBeFalse();
});
