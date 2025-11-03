<?php

declare(strict_types=1);

use App\Models\User;
use App\Policies\UserPolicy;
use Tests\TestDoubles\TestCheckUserIsAdminQuery;

it('allows admin users to view any users', function (): void {
    $adminUser = User::factory()->create();

    $mockQuery = new TestCheckUserIsAdminQuery(true);

    $policy = new UserPolicy(checkUserIsAdminQuery: $mockQuery);
    expect($policy->viewAny($adminUser))->toBeTrue();
});

it('denies non-admin users to view any users', function (): void {
    $regularUser = User::factory()->create();

    $mockQuery = new TestCheckUserIsAdminQuery(false);

    $policy = new UserPolicy(checkUserIsAdminQuery: $mockQuery);
    expect($policy->viewAny($regularUser))->toBeFalse();
});

it('allows admin users to view specific users', function (): void {
    $adminUser = User::factory()->create();

    $mockQuery = new TestCheckUserIsAdminQuery(true);

    $policy = new UserPolicy(checkUserIsAdminQuery: $mockQuery);
    expect($policy->view($adminUser))->toBeTrue();
});

it('denies non-admin users to view specific users', function (): void {
    $regularUser = User::factory()->create();

    $mockQuery = new TestCheckUserIsAdminQuery(false);

    $policy = new UserPolicy(checkUserIsAdminQuery: $mockQuery);
    expect($policy->view($regularUser))->toBeFalse();
});

it('allows admin users to create users', function (): void {
    $adminUser = User::factory()->create();

    $mockQuery = new TestCheckUserIsAdminQuery(true);

    $policy = new UserPolicy(checkUserIsAdminQuery: $mockQuery);
    expect($policy->create($adminUser))->toBeTrue();
});

it('denies non-admin users to create users', function (): void {
    $regularUser = User::factory()->create();

    $mockQuery = new TestCheckUserIsAdminQuery(false);

    $policy = new UserPolicy(checkUserIsAdminQuery: $mockQuery);
    expect($policy->create($regularUser))->toBeFalse();
});

it('allows admin users to update users', function (): void {
    $adminUser = User::factory()->create();

    $mockQuery = new TestCheckUserIsAdminQuery(true);

    $policy = new UserPolicy(checkUserIsAdminQuery: $mockQuery);
    expect($policy->update($adminUser))->toBeTrue();
});

it('denies non-admin users to update users', function (): void {
    $regularUser = User::factory()->create();

    $mockQuery = new TestCheckUserIsAdminQuery(false);

    $policy = new UserPolicy(checkUserIsAdminQuery: $mockQuery);
    expect($policy->update($regularUser))->toBeFalse();
});

it('allows admin users to delete users', function (): void {
    $adminUser = User::factory()->create();

    $mockQuery = new TestCheckUserIsAdminQuery(true);

    $policy = new UserPolicy(checkUserIsAdminQuery: $mockQuery);
    expect($policy->delete($adminUser))->toBeTrue();
});

it('denies non-admin users to delete users', function (): void {
    $regularUser = User::factory()->create();

    $mockQuery = new TestCheckUserIsAdminQuery(false);

    $policy = new UserPolicy(checkUserIsAdminQuery: $mockQuery);
    expect($policy->delete($regularUser))->toBeFalse();
});

it('allows admin users to restore users', function (): void {
    $adminUser = User::factory()->create();

    $mockQuery = new TestCheckUserIsAdminQuery(true);

    $policy = new UserPolicy(checkUserIsAdminQuery: $mockQuery);
    expect($policy->restore($adminUser))->toBeTrue();
});

it('denies non-admin users to restore users', function (): void {
    $regularUser = User::factory()->create();

    $mockQuery = new TestCheckUserIsAdminQuery(false);

    $policy = new UserPolicy(checkUserIsAdminQuery: $mockQuery);
    expect($policy->restore($regularUser))->toBeFalse();
});

it('allows admin users to force delete users', function (): void {
    $adminUser = User::factory()->create();

    $mockQuery = new TestCheckUserIsAdminQuery(true);

    $policy = new UserPolicy(checkUserIsAdminQuery: $mockQuery);
    expect($policy->forceDelete($adminUser))->toBeTrue();
});

it('denies non-admin users to force delete users', function (): void {
    $regularUser = User::factory()->create();

    $mockQuery = new TestCheckUserIsAdminQuery(false);

    $policy = new UserPolicy(checkUserIsAdminQuery: $mockQuery);
    expect($policy->forceDelete($regularUser))->toBeFalse();
});
