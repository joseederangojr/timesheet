<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Client;

final readonly class FindClientByIdQuery
{
    public function handle(int $id): Client
    {
        return Client::query()->findOrFail($id);
    }
}
