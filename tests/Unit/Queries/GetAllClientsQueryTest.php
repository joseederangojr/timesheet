<?php

declare(strict_types=1);

use App\Models\Client;
use App\Queries\GetAllClientsQuery;

it('returns all clients ordered by name', function (): void {
    // Create clients with names in reverse alphabetical order
    Client::factory()->create(['name' => 'Zeta Corp']);
    Client::factory()->create(['name' => 'Alpha Inc']);
    Client::factory()->create(['name' => 'Beta LLC']);

    $query = new GetAllClientsQuery();
    $result = $query->handle();

    expect($result)->toHaveCount(3);
    expect($result->first()->name)->toBe('Alpha Inc');
    expect($result->last()->name)->toBe('Zeta Corp');

    // Verify they are ordered by name
    $names = $result->pluck('name')->toArray();
    expect($names)->toBe(['Alpha Inc', 'Beta LLC', 'Zeta Corp']);
});

it('returns empty collection when no clients exist', function (): void {
    $query = new GetAllClientsQuery();
    $result = $query->handle();

    expect($result)->toBeEmpty();
    expect($result)->toBeInstanceOf(
        Illuminate\Database\Eloquent\Collection::class,
    );
});

it('returns clients with correct model instances', function (): void {
    Client::factory()->create(['name' => 'Test Client']);

    $query = new GetAllClientsQuery();
    $result = $query->handle();

    expect($result)->toHaveCount(1);
    expect($result->first())->toBeInstanceOf(Client::class);
    expect($result->first()->name)->toBe('Test Client');
});
