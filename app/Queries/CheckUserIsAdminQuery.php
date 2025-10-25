<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;

final readonly class CheckUserIsAdminQuery
{
    public function __construct(
        private CheckUserHasRoleQuery $checkUserHasRoleQuery,
    ) {
        //
    }

    public function handle(User $user): bool
    {
        return $this->checkUserHasRoleQuery->handle($user, 'admin');
    }
}
