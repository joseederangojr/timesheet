<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Employment;

final readonly class GetEmploymentByIdQuery
{
    public function handle(int $id): Employment
    {
        return Employment::query()
            ->with(['user', 'client'])
            ->findOrFail($id);
    }
}
