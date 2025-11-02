<?php

declare(strict_types=1);

namespace App\Queries;

use App\Data\UserFilters;
use App\Models\User;
use App\Queries\Filters\PaginationFilter;
use App\Queries\Filters\RoleFilter;
use App\Queries\Filters\SearchFilter;
use App\Queries\Filters\SortFilter;
use App\Queries\Filters\VerificationFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class GetUsersQuery
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function handle(UserFilters $filters): LengthAwarePaginator
    {
        return User::query()
            ->with('roles')
            ->tap(new SearchFilter(['name', 'email'], $filters->search))
            ->tap(new RoleFilter($filters->role))
            ->tap(new VerificationFilter($filters->verified))
            ->tap(new SortFilter(['name', 'email', 'created_at', 'email_verified_at'], $filters->sortBy, $filters->sortDirection))
            ->pipe(new PaginationFilter($filters->perPage));
    }
}
