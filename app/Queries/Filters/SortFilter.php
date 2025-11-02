<?php

declare(strict_types=1);

namespace App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;

final readonly class SortFilter
{
    /**
     * @param  array<string>  $allowedSortFields
     */
    public function __construct(
        private array $allowedSortFields,
        private string $sortBy = 'created_at',
        private string $sortDirection = 'desc'
    ) {}

    public function __invoke(Builder $query): void
    {
        $sortBy = in_array($this->sortBy, $this->allowedSortFields, true)
            ? $this->sortBy
            : $this->allowedSortFields[0] ?? 'created_at';
        $sortDirection = in_array($this->sortDirection, ['asc', 'desc'], true)
            ? $this->sortDirection
            : 'desc';

        $query->orderBy($sortBy, $sortDirection);
    }
}
