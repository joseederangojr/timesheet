<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetRolesByNamesQuery
{
    /**
     * @param  string[]  $names
     * @return Collection<int, Role>
     */
    public function handle(array $names): Collection
    {
        return Role::query()->whereIn('name', $names)->get();
    }
}
