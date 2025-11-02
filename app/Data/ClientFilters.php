<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Http\Request;

final readonly class ClientFilters
{
    public function __construct(
        public ?string $search = null,
        public string $sortBy = 'created_at',
        public string $sortDirection = 'desc',
        public ?string $status = null,
        public ?string $perPage = '15',
    ) {}

    public static function fromRequest(Request $request): self
    {
        $search = $request->string('search');
        $search = $search->isEmpty() ? null : (string) $search;

        $status = $request->string('status');
        $status = $status->isEmpty() ? null : (string) $status;

        $perPage = $request->string('per_page');
        $perPage = $perPage->isEmpty() ? null : (string) $perPage;

        return new self(
            search: $search,
            sortBy: (string) $request->string('sort_by', 'created_at'),
            sortDirection: (string) $request->string('sort_direction', 'desc'),
            status: $status,
            perPage: $perPage,
        );
    }
}
