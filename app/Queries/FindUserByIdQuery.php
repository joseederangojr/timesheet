<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;

final readonly class FindUserByIdQuery
{
    public function handle(int $id): User
    {
        return User::query()->findOrFail($id);
    }
}
