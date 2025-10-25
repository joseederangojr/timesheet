<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;

final readonly class CheckUserHasAnyRoleQuery
{
    /**
     * @param  array<string>  $roleNames
     */
    public function handle(User $user, array $roleNames): bool
    {
        return $user->roles()->whereIn('name', $roleNames)->exists();
    }
}
