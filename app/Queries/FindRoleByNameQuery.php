<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Role;

final readonly class FindRoleByNameQuery
{
    public function handle(string $name): ?Role
    {
        return Role::query()->where('name', $name)->first();
    }
}
