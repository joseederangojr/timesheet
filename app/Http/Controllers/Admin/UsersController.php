<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\CreateUser;
use App\Data\CreateUserData;
use App\Data\UserFilters;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Queries\GetAllRolesQuery;
use App\Queries\GetRolesByNamesQuery;
use App\Queries\GetUsersQuery;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

final readonly class UsersController
{
    public function index(
        Request $request,
        GetUsersQuery $getUsersQuery,
        GetAllRolesQuery $getAllRolesQuery,
    ): Response {
        $filters = UserFilters::fromRequest($request);
        $users = $getUsersQuery->handle($filters)->withQueryString();

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
            'roles' => $getAllRolesQuery->handle(),
        ]);
    }

    public function create(GetAllRolesQuery $getAllRolesQuery): Response
    {
        return Inertia::render('admin/users/create', [
            'roles' => $getAllRolesQuery->handle(),
        ]);
    }

    public function store(
        CreateUserRequest $request,
        GetRolesByNamesQuery $getRolesByNamesQuery,
        CreateUser $createUser,
    ): RedirectResponse {
        try {
            /** @var array{name: string, email: string, password: string, roles: string[]} $validated */
            $validated = $request->validated();

            $roles = $getRolesByNamesQuery->handle($validated['roles']);

            $data = new CreateUserData(
                name: $validated['name'],
                email: $validated['email'],
                password: $validated['password'],
                roles: $roles,
            );

            $createUser->handle($data);

            return to_route('admin.users.index')
                ->with('status', ['type' => 'success', 'message' => __('User created successfully.')]);

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('status', ['type' => 'error', 'message' => __('Failed to create user. Please try again.')]);
        }
    }
}
