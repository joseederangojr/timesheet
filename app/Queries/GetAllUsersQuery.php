<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetAllUsersQuery
{
    /**
     * @return Collection<int, User>
     */
    public function handle(): Collection
    {
        return User::query()
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }
}
