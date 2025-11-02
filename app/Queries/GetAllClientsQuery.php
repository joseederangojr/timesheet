<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetAllClientsQuery
{
    /**
     * @return Collection<int, Client>
     */
    public function handle(): Collection
    {
        return Client::query()->orderBy('name')->get();
    }
}
