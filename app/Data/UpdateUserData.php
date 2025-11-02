<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final readonly class UpdateUserData
{
    /**
     * @param  Collection<int, Role>  $roles
     */
    public function __construct(
        public User $user,
        public string $name,
        public string $email,
        public Collection $roles,
    ) {
        //
    }
}
