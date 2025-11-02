<?php

declare(strict_types=1);

namespace App\Queries;

use App\Data\ClientFilters;
use App\Models\Client;
use App\Queries\Filters\PaginationFilter;
use App\Queries\Filters\SearchFilter;
use App\Queries\Filters\SortFilter;
use App\Queries\Filters\StatusFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class GetClientsQuery
{
    private const array SEARCHABLE = [
        'name',
        'email',
        'contact_person',
        'industry',
    ];

    private const array SORTABLE = [
        'name',
        'email',
        'status',
        'industry',
        'created_at',
        'updated_at',
    ];

    /**
     * @return LengthAwarePaginator<int, Client>
     */
    public function handle(ClientFilters $filters): LengthAwarePaginator
    {
        /** @var PaginationFilter<Client> $pagination */
        $pagination = new PaginationFilter($filters->perPage);

        return Client::query()
            ->tap(new SearchFilter(self::SEARCHABLE, $filters->search))
            ->tap(new StatusFilter($filters->status))
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
