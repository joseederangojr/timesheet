<?php

declare(strict_types=1);

namespace App\Queries;

use App\Data\UserFilters;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final readonly class GetUsersQuery
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function handle(UserFilters $filters): LengthAwarePaginator
    {
        // Validate sort parameters
        $allowedSortFields = [
            'name',
            'email',
            'created_at',
            'email_verified_at',
        ];
        $sortBy = in_array($filters->sortBy, $allowedSortFields)
            ? $filters->sortBy
            : 'created_at';
        $sortDirection = in_array($filters->sortDirection, ['asc', 'desc'])
            ? $filters->sortDirection
            : 'desc';

        return User::query()
            ->with('roles')
            ->when($filters->search, function (
                Builder $query,
                string $search,
            ): void {
                $query
                    ->where('name', 'like', sprintf('%%%s%%', $search))
                    ->orWhere('email', 'like', sprintf('%%%s%%', $search));
            })
            ->when($filters->role, function (
                Builder $query,
                string $role,
            ): void {
                $query->whereHas('roles', function (Builder $roleQuery) use (
                    $role,
                ): void {
                    $roleQuery->where('name', $role);
                });
            })
            ->when($filters->verified, function (
                Builder $query,
                string $verified,
            ): void {
                if ($verified === 'verified') {
                    $query->whereNotNull('email_verified_at');
                } elseif ($verified === 'unverified') {
                    $query->whereNull('email_verified_at');
                }
            })
            ->orderBy($sortBy, $sortDirection)
            ->paginate(perPage: (int) ($filters->perPage ?: 15))
            ->withQueryString();
    }
}
