<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\CreateUser;
use App\Actions\UpdateUser;
use App\Data\CreateUserData;
use App\Data\UpdateUserData;
use App\Data\UserFilters;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Queries\CheckUserIsAdminQuery;
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
        CheckUserIsAdminQuery $checkUserIsAdminQuery,
    ): Response {
        $user = $request->user();
        abort_if(! $user || ! $checkUserIsAdminQuery->handle($user), 403);

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

    public function create(
        Request $request,
        GetAllRolesQuery $getAllRolesQuery,
        CheckUserIsAdminQuery $checkUserIsAdminQuery,
    ): Response {
        $user = $request->user();
        abort_if(! $user || ! $checkUserIsAdminQuery->handle($user), 403);

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

            return to_route('admin.users.index')->with('status', [
                'type' => 'success',
                'message' => __('User created successfully.'),
            ]);
        } catch (Exception) {
            return back()
                ->withInput()
                ->with('status', [
                    'type' => 'error',
                    'message' => __('Failed to create user. Please try again.'),
                ]);
        }
    }

    public function show(
        Request $request,
        User $user,
        CheckUserIsAdminQuery $checkUserIsAdminQuery,
    ): Response {
        $authUser = $request->user();
        abort_if(! $authUser || ! $checkUserIsAdminQuery->handle($authUser), 403);

        return Inertia::render('admin/users/show', [
            'user' => $user->load('roles'),
        ]);
    }

    public function edit(
        Request $request,
        User $user,
        GetAllRolesQuery $getAllRolesQuery,
        CheckUserIsAdminQuery $checkUserIsAdminQuery,
    ): Response {
        $authUser = $request->user();
        abort_if(! $authUser || ! $checkUserIsAdminQuery->handle($authUser), 403);

        return Inertia::render('admin/users/edit', [
            'user' => $user->load('roles'),
            'roles' => $getAllRolesQuery->handle(),
        ]);
    }

    public function update(
        UpdateUserRequest $request,
        User $user,
        GetRolesByNamesQuery $getRolesByNamesQuery,
        UpdateUser $updateUser,
    ): RedirectResponse {
        try {
            /** @var array{name: string, email: string, roles: string[]} $validated */
            $validated = $request->validated();

            $roles = $getRolesByNamesQuery->handle($validated['roles']);

            $data = new UpdateUserData(
                user: $user,
                name: $validated['name'],
                email: $validated['email'],
                roles: $roles,
            );

            $updateUser->handle($data);

            return to_route('admin.users.index')->with('status', [
                'type' => 'success',
                'message' => __('User updated successfully.'),
            ]);
        } catch (Exception) {
            return back()
                ->withInput()
                ->with('status', [
                    'type' => 'error',
                    'message' => __('Failed to update user. Please try again.'),
                ]);
        }
    }
}
