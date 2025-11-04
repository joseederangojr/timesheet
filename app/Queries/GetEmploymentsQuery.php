<?php

declare(strict_types=1);

namespace App\Queries;

use App\Data\EmploymentFilters;
use App\Models\Employment;
use App\Queries\Filters\ClientFilter;
use App\Queries\Filters\PaginationFilter;
use App\Queries\Filters\SearchFilter;
use App\Queries\Filters\SortFilter;
use App\Queries\Filters\StatusFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class GetEmploymentsQuery
{
    private const array SEARCHABLE = ['position', 'work_location'];

    private const array SORTABLE = [
        'position',
        'hire_date',
        'status',
        'salary',
        'work_location',
        'effective_date',
        'end_date',
        'created_at',
    ];

    /**
     * @return LengthAwarePaginator<int, Employment>
     */
    public function handle(EmploymentFilters $filters): LengthAwarePaginator
    {
        /** @var PaginationFilter<Employment> $pagination */
        $pagination = new PaginationFilter($filters->perPage);

        return Employment::query()
            ->with(['user', 'client'])
            ->tap(new SearchFilter(self::SEARCHABLE, $filters->search))
            ->tap(new StatusFilter($filters->status))
            ->tap(new ClientFilter($filters->client))
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
