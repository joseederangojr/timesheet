<?php

declare(strict_types=1);

namespace App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
final readonly class StatusFilter
{
    public function __construct(private ?string $status) {}

    /**
     * @param Builder<covariant TModel> $query
     */
    public function __invoke(Builder $query): void
    {
        $query->when($this->status, function (Builder $query): void {
            $query->where('status', $this->status);
        });
    }
}
