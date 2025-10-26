<?php

declare(strict_types=1);

namespace App\Queries;

use App\DTOs\UserFilters;
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
            ->orderBy($sortBy, $sortDirection)
            ->paginate(15)
            ->withQueryString();
    }
}
