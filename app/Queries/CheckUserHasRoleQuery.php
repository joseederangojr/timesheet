<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;

final readonly class CheckUserHasRoleQuery
{
    public function handle(User $user, string $roleName): bool
    {
        return $user->roles()->where('name', $roleName)->exists();
    }
}
