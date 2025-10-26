<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Http\Request;

final readonly class UserFilters
{
    public function __construct(
        public ?string $search = null,
        public string $sortBy = 'created_at',
        public string $sortDirection = 'desc',
    ) {}

    public static function fromRequest(Request $request): self
    {
        $search = $request->string('search');
        $search = $search->isEmpty() ? null : (string) $search;

        return new self(
            search: $search,
            sortBy: (string) $request->string('sort_by', 'created_at'),
            sortDirection: (string) $request->string('sort_direction', 'desc'),
        );
    }
}
