<?php

declare(strict_types=1);

namespace App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
final readonly class ClientFilter
{
    public function __construct(private ?string $client) {}

    /**
     * @param Builder<covariant TModel> $query
     */
    public function __invoke(Builder $query): void
    {
        $query->when($this->client, function (Builder $query): void {
            $query->whereHas('client', function (Builder $clientQuery): void {
                $clientQuery->where('name', $this->client);
            });
        });
    }
}
