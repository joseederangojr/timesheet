<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;

interface CheckUserHasRoleQueryInterface
{
    public function handle(User $user, string $roleName): bool;
}
