<?php

declare(strict_types=1);

namespace App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;

final readonly class RoleFilter
{
    public function __construct(private ?string $role) {}

    public function __invoke(Builder $query): void
    {
        $query->when($this->role, function (Builder $query) {
            $query->whereHas('roles', function (Builder $roleQuery) {
                $roleQuery->where('name', $this->role);
            });
        });
    }
}
