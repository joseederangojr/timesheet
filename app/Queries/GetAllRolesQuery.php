<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetAllRolesQuery
{
    /**
     * @return Collection<int, Role>
     */
    public function handle(): Collection
    {
        return Role::query()->select('id', 'name')->get();
    }
}
