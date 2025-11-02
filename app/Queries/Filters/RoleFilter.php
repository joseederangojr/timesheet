<?php

declare(strict_types=1);

namespace App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
final readonly class RoleFilter
{
    public function __construct(private ?string $role) {}

    /**
     * @param Builder<covariant TModel> $query
     */
    public function __invoke(Builder $query): void
    {
        $query->when($this->role, function (Builder $query): void {
            $query->whereHas('roles', function (Builder $roleQuery): void {
                $roleQuery->where('name', $this->role);
            });
        });
    }
}
