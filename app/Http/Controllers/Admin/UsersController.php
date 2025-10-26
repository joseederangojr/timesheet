<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Queries\GetUsersQuery;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class UsersController
{
    public function __construct(private GetUsersQuery $getUsersQuery) {}

    public function index(Request $request): Response
    {
        $users = $this->getUsersQuery->handle($request);

        return Inertia::render('admin/users/index', [
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }
}
