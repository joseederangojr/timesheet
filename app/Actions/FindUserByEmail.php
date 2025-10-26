<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

final readonly class FindUserByEmail
{
    public function handle(string $email): User
    {
        return User::query()
            ->where('email', $email)
            ->firstOrFail();
    }
}
