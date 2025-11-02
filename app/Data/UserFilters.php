<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Http\Request;

final readonly class UserFilters
{
    public function __construct(
        public ?string $search = null,
        public ?string $sortBy = 'created_at',
        public ?string $sortDirection = 'desc',
        public ?string $role = null,
        public ?string $verified = null,
        public ?string $perPage = '15',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: (string) $request->input('search', ''),
            role: (string) $request->input('role', ''),
            verified: (string) $request->input('verified', ''),
            sortBy: (string) $request->input('sort_by', 'created_at'),
            sortDirection: (string) $request->input('sort_direction', 'desc'),
            perPage: (string) $request->input('per_page', '15'),
        );
    }
}
