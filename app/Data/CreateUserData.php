<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

final readonly class CreateUserData
{
    /**
     * @param  Collection<int, Role>  $roles
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public Collection $roles,
    ) {
        //
    }
}
