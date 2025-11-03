<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;

interface CheckUserIsAdminQueryInterface
{
    public function handle(User $user): bool;
}
