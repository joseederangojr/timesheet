<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DTOs\UserFilters;
use App\Queries\GetUsersQuery;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class UsersController
{
    public function __construct(private GetUsersQuery $getUsersQuery) {}

    public function index(Request $request): Response
    {
        $filters = UserFilters::fromRequest($request);
        $users = $this->getUsersQuery->handle($filters)->withQueryString();

        return Inertia::render('admin/users/index', [
            'users' => fn () => $users,
            'filters' => $request->only([
                'search',
                'sort_by',
                'sort_direction',
                'role',
                'verified',
                'page',
                'per_page',
            ]),
        ]);
    }
}
