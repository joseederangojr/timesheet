<?php

declare(strict_types=1);

use App\Data\UserFilters;
use Illuminate\Http\Request;

it('creates filters from request with all parameters', function (): void {
    $request = new Request([
        'search' => 'test search',
        'sort_by' => 'name',
        'sort_direction' => 'asc',
        'role' => 'admin',
        'verified' => '1',
        'per_page' => '25',
    ]);

    $filters = UserFilters::fromRequest($request);

    expect($filters->search)->toBe('test search');
    expect($filters->sortBy)->toBe('name');
    expect($filters->sortDirection)->toBe('asc');
    expect($filters->role)->toBe('admin');
    expect($filters->verified)->toBe('1');
    expect($filters->perPage)->toBe('25');
});

it('creates filters from request with empty parameters', function (): void {
    $request = new Request([
        'search' => '',
        'role' => '',
        'verified' => '',
        'per_page' => '',
    ]);

    $filters = UserFilters::fromRequest($request);

    expect($filters->search)->toBe('');
    expect($filters->sortBy)->toBe('created_at');
    expect($filters->sortDirection)->toBe('desc');
    expect($filters->role)->toBe('');
    expect($filters->verified)->toBe('');
    expect($filters->perPage)->toBe('');
});

it('creates filters from request with missing parameters', function (): void {
    $request = new Request();

    $filters = UserFilters::fromRequest($request);

    expect($filters->search)->toBe('');
    expect($filters->sortBy)->toBe('created_at');
    expect($filters->sortDirection)->toBe('desc');
    expect($filters->role)->toBe('');
    expect($filters->verified)->toBe('');
    expect($filters->perPage)->toBe('15');
});
