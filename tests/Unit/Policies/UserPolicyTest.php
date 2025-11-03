<?php

declare(strict_types=1);

use App\Models\User;
use App\Policies\UserPolicy;
use App\Queries\CheckUserIsAdminQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed();
});

it('allows admin users to view any users', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new UserPolicy(checkUserIsAdminQuery: $query);
    expect($policy->viewAny($adminUser))->toBeTrue();
});

it('denies non-admin users to view any users', function (): void {
    $regularUser = User::factory()->create();

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new UserPolicy(checkUserIsAdminQuery: $query);
    expect($policy->viewAny($regularUser))->toBeFalse();
});

it('allows admin users to view specific users', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new UserPolicy(checkUserIsAdminQuery: $query);
    expect($policy->view($adminUser))->toBeTrue();
});

it('denies non-admin users to view specific users', function (): void {
    $regularUser = User::factory()->create();

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new UserPolicy(checkUserIsAdminQuery: $query);
    expect($policy->view($regularUser))->toBeFalse();
});

it('allows admin users to create users', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new UserPolicy(checkUserIsAdminQuery: $query);
    expect($policy->create($adminUser))->toBeTrue();
});

it('denies non-admin users to create users', function (): void {
    $regularUser = User::factory()->create();

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new UserPolicy(checkUserIsAdminQuery: $query);
    expect($policy->create($regularUser))->toBeFalse();
});

it('allows admin users to update users', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new UserPolicy(checkUserIsAdminQuery: $query);
    expect($policy->update($adminUser))->toBeTrue();
});

it('denies non-admin users to update users', function (): void {
    $regularUser = User::factory()->create();

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new UserPolicy(checkUserIsAdminQuery: $query);
    expect($policy->update($regularUser))->toBeFalse();
});

it('allows admin users to delete users', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new UserPolicy(checkUserIsAdminQuery: $query);
    expect($policy->delete($adminUser))->toBeTrue();
});

it('denies non-admin users to delete users', function (): void {
    $regularUser = User::factory()->create();

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new UserPolicy(checkUserIsAdminQuery: $query);
    expect($policy->delete($regularUser))->toBeFalse();
});

it('allows admin users to restore users', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new UserPolicy(checkUserIsAdminQuery: $query);
    expect($policy->restore($adminUser))->toBeTrue();
});

it('denies non-admin users to restore users', function (): void {
    $regularUser = User::factory()->create();

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new UserPolicy(checkUserIsAdminQuery: $query);
    expect($policy->restore($regularUser))->toBeFalse();
});

it('allows admin users to force delete users', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new UserPolicy(checkUserIsAdminQuery: $query);
    expect($policy->forceDelete($adminUser))->toBeTrue();
});

it('denies non-admin users to force delete users', function (): void {
    $regularUser = User::factory()->create();

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new UserPolicy(checkUserIsAdminQuery: $query);
    expect($policy->forceDelete($regularUser))->toBeFalse();
});
