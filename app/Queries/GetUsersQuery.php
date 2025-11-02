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
    private const array SEARCHABLE = ['name', 'email'];

    private const array SORTABLE = [
        'name',
        'email',
        'created_at',
        'email_verified_at',
    ];

    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function handle(UserFilters $filters): LengthAwarePaginator
    {
        /** @var PaginationFilter<User> $pagination */
        $pagination = new PaginationFilter($filters->perPage);

        return User::query()
            ->with('roles')
            ->tap(new SearchFilter(self::SEARCHABLE, $filters->search))
            ->tap(new RoleFilter($filters->role))
            ->tap(new VerificationFilter($filters->verified))
            ->tap(
                new SortFilter(
                    self::SORTABLE,
                    $filters->sortBy,
                    $filters->sortDirection,
                ),
            )
            ->pipe($pagination);
    }
}
