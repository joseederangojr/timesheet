<?php

declare(strict_types=1);

namespace App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;

final readonly class SearchFilter
{
    /**
     * @param  array<string>  $searchableFields
     */
    public function __construct(
        private array $searchableFields,
        private ?string $search
    ) {}

    public function __invoke(Builder $query): void
    {
        $query->when($this->search, function (Builder $query) {
            $query->where(function (Builder $subQuery) {
                foreach ($this->searchableFields as $field) {
                    $subQuery->orWhere($field, 'like', sprintf('%%%s%%', $this->search));
                }
            });
        });
    }
}
