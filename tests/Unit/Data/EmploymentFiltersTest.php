<?php

declare(strict_types=1);

use App\Data\EmploymentFilters;
use Illuminate\Http\Request;

it('creates filters from request with all parameters', function (): void {
    $request = new Request([
        'search' => 'test search',
        'sort_by' => 'position',
        'sort_direction' => 'asc',
        'status' => 'active',
        'client' => '1',
        'per_page' => '25',
    ]);

    $filters = EmploymentFilters::fromRequest($request);

    expect($filters->search)->toBe('test search');
    expect($filters->sortBy)->toBe('position');
    expect($filters->sortDirection)->toBe('asc');
    expect($filters->status)->toBe('active');
    expect($filters->client)->toBe('1');
    expect($filters->perPage)->toBe('25');
});

it('creates filters from request with empty parameters', function (): void {
    $request = new Request([
        'search' => '',
        'status' => '',
        'client' => '',
        'per_page' => '',
    ]);

    $filters = EmploymentFilters::fromRequest($request);

    expect($filters->search)->toBe('');
    expect($filters->sortBy)->toBe('created_at');
    expect($filters->sortDirection)->toBe('desc');
    expect($filters->status)->toBe('');
    expect($filters->client)->toBe('');
    expect($filters->perPage)->toBe('');
});

it('creates filters from request with missing parameters', function (): void {
    $request = new Request();

    $filters = EmploymentFilters::fromRequest($request);

    expect($filters->search)->toBe('');
    expect($filters->sortBy)->toBe('created_at');
    expect($filters->sortDirection)->toBe('desc');
    expect($filters->status)->toBe('');
    expect($filters->client)->toBe('');
    expect($filters->perPage)->toBe('15');
});
