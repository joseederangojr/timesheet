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
        $search = $search->isEmpty() ? null : (string) $search;

        $role = $request->string('role');
        $role = $role->isEmpty() ? null : (string) $role;

        $verified = $request->string('verified');
        $verified = $verified->isEmpty() ? null : (string) $verified;

        $perPage = $request->string('per_page');
        $perPage = $perPage->isEmpty() ? null : (string) $perPage;

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
