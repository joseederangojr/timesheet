<?php

declare(strict_types=1);

namespace App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
final readonly class VerificationFilter
{
    public function __construct(private ?string $verified) {}

    /**
     * @param Builder<covariant TModel> $query
     */
    public function __invoke(Builder $query): void
    {
        $query
            ->when(
                $this->verified === 'verified',
                fn (Builder $query) => $query->whereNotNull('email_verified_at'),
            )
            ->when(
                $this->verified === 'unverified',
                fn (Builder $query) => $query->whereNull('email_verified_at'),
            );
    }
}
