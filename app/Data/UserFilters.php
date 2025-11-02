<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Http\Request;

final readonly class UserFilters
{
    public function __construct(
        public ?string $search = null,
        public string $sortBy = 'created_at',
        public string $sortDirection = 'desc',
        public ?string $role = null,
        public ?string $verified = null,
        public ?string $perPage = '15',
    ) {}

    public static function fromRequest(Request $request): self
    {
        $search = $request->string('search');
        $search = (string) $search !== '' ? (string) $search : null;

        $role = $request->string('role');
        $role = (string) $role !== '' ? (string) $role : null;

        $verified = $request->string('verified');
        $verified = (string) $verified !== '' ? (string) $verified : null;

        $perPage = $request->string('per_page');
        $perPage = (string) $perPage !== '' ? (string) $perPage : null;

        return new self(
            search: $search,
            sortBy: (string) $request->string('sort_by', 'created_at'),
            sortDirection: (string) $request->string('sort_direction', 'desc'),
            role: $role,
            verified: $verified,
            perPage: $perPage,
        );
    }
}
