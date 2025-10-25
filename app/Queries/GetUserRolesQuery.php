<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;

final readonly class GetUserRolesQuery
{
    /**
     * @return array<string>
     */
    public function handle(User $user): array
    {
        /** @var array<string> */
        return $user->roles()->pluck('name')->toArray();
    }
}
