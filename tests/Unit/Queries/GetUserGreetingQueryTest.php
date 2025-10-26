<?php

declare(strict_types=1);

use App\Models\User;
use App\Queries\CheckUserIsAdminQuery;
use App\Queries\CheckUserIsEmployeeQuery;
use App\Queries\GetUserGreetingQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed();
});

it('returns admin greeting for admin users', function (): void {
    $adminUser = User::factory()->create();
    $adminUser->roles()->attach(1); // Admin role ID

    $checkUserIsAdminQuery = app(CheckUserIsAdminQuery::class);
    $checkUserIsEmployeeQuery = app(CheckUserIsEmployeeQuery::class);

    $query = new GetUserGreetingQuery(
        $checkUserIsAdminQuery,
        $checkUserIsEmployeeQuery,
    );

    $greeting = $query->handle($adminUser);

    expect($greeting)->toBe('Hello, '.$adminUser->name);
});

it('returns employee greeting for employee users', function (): void {
    $employeeUser = User::factory()->create();
    $employeeUser->roles()->attach(2); // Employee role ID

    $checkUserIsAdminQuery = app(CheckUserIsAdminQuery::class);
    $checkUserIsEmployeeQuery = app(CheckUserIsEmployeeQuery::class);

    $query = new GetUserGreetingQuery(
        $checkUserIsAdminQuery,
        $checkUserIsEmployeeQuery,
    );

    $greeting = $query->handle($employeeUser);

    expect($greeting)->toBe('Hi, '.$employeeUser->name);
});

it('returns default greeting for regular users', function (): void {
    $regularUser = User::factory()->create();
    // No roles assigned

    $checkUserIsAdminQuery = app(CheckUserIsAdminQuery::class);
    $checkUserIsEmployeeQuery = app(CheckUserIsEmployeeQuery::class);

    $query = new GetUserGreetingQuery(
        $checkUserIsAdminQuery,
        $checkUserIsEmployeeQuery,
    );

    $greeting = $query->handle($regularUser);

    expect($greeting)->toBe('Welcome, '.$regularUser->name);
});
