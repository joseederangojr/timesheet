<?php

declare(strict_types=1);

namespace App\Queries\Filters;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final readonly class PaginationFilter
{
    public function __construct(private ?string $perPage = '15') {}

    public function __invoke(Builder $query): LengthAwarePaginator
    {
        return $query->paginate(perPage: (int) ($this->perPage ?: 15))
            ->withQueryString();
    }
}
