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
        $search = $request->input('search');
        $search = empty($search) ? null : (string) $search;

        $role = $request->input('role');
        $role = empty($role) ? null : (string) $role;

        $verified = $request->input('verified');
        $verified = empty($verified) ? null : (string) $verified;

        $perPage = $request->input('per_page');
        $perPage = empty($perPage) ? null : (string) $perPage;

        return new self(
            search: $search,
            sortBy: (string) $request->input('sort_by', 'created_at'),
            sortDirection: (string) $request->input('sort_direction', 'desc'),
            role: $role,
            verified: $verified,
            perPage: $perPage,
        );
    }
}
