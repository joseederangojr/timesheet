<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Http\Request;

final readonly class ClientFilters
{
    public function __construct(
        public ?string $search = null,
        public ?string $sortBy = 'created_at',
        public ?string $sortDirection = 'desc',
        public ?string $status = null,
        public ?string $perPage = '15',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: (string) $request->string('search', ''),
            sortBy: (string) $request->string('sort_by', 'created_at'),
            sortDirection: (string) $request->string('sort_direction', 'desc'),
            status: (string) $request->string('status', ''),
            perPage: (string) $request->string('per_page', '15'),
        );
    }
}
