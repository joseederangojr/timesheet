<?php

declare(strict_types=1);

use App\Models\User;
use App\Policies\ClientPolicy;
use App\Queries\CheckUserIsAdminQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed();
});

it('allows admin users to view any clients', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new ClientPolicy(checkUserIsAdminQuery: $query);
    expect($policy->viewAny($adminUser))->toBeTrue();
});

it('denies non-admin users to view any clients', function (): void {
    $regularUser = User::factory()->create();

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new ClientPolicy(checkUserIsAdminQuery: $query);
    expect($policy->viewAny($regularUser))->toBeFalse();
});

it('allows admin users to view specific clients', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new ClientPolicy(checkUserIsAdminQuery: $query);
    expect($policy->view($adminUser))->toBeTrue();
});

it('denies non-admin users to view specific clients', function (): void {
    $regularUser = User::factory()->create();

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new ClientPolicy(checkUserIsAdminQuery: $query);
    expect($policy->view($regularUser))->toBeFalse();
});

it('allows admin users to create clients', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new ClientPolicy(checkUserIsAdminQuery: $query);
    expect($policy->create($adminUser))->toBeTrue();
});

it('denies non-admin users to create clients', function (): void {
    $regularUser = User::factory()->create();

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new ClientPolicy(checkUserIsAdminQuery: $query);
    expect($policy->create($regularUser))->toBeFalse();
});

it('allows admin users to update clients', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new ClientPolicy(checkUserIsAdminQuery: $query);
    expect($policy->update($adminUser))->toBeTrue();
});

it('denies non-admin users to update clients', function (): void {
    $regularUser = User::factory()->create();

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new ClientPolicy(checkUserIsAdminQuery: $query);
    expect($policy->update($regularUser))->toBeFalse();
});

it('allows admin users to delete clients', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new ClientPolicy(checkUserIsAdminQuery: $query);
    expect($policy->delete($adminUser))->toBeTrue();
});

it('denies non-admin users to delete clients', function (): void {
    $regularUser = User::factory()->create();

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new ClientPolicy(checkUserIsAdminQuery: $query);
    expect($policy->delete($regularUser))->toBeFalse();
});

it('allows admin users to restore clients', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new ClientPolicy(checkUserIsAdminQuery: $query);
    expect($policy->restore($adminUser))->toBeTrue();
});

it('denies non-admin users to restore clients', function (): void {
    $regularUser = User::factory()->create();

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new ClientPolicy(checkUserIsAdminQuery: $query);
    expect($policy->restore($regularUser))->toBeFalse();
});

it('allows admin users to force delete clients', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new ClientPolicy(checkUserIsAdminQuery: $query);
    expect($policy->forceDelete($adminUser))->toBeTrue();
});

it('denies non-admin users to force delete clients', function (): void {
    $regularUser = User::factory()->create();

    $query = app(CheckUserIsAdminQuery::class);
    $policy = new ClientPolicy(checkUserIsAdminQuery: $query);
    expect($policy->forceDelete($regularUser))->toBeFalse();
});
