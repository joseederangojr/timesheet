<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\CreateEmployment;
use App\Actions\DeleteEmployment;
use App\Actions\EndEmployment;
use App\Actions\UpdateEmployment;
use App\Data\CreateEmploymentData;
use App\Data\EmploymentFilters;
use App\Data\UpdateEmploymentData;
use App\Enums\EmploymentStatus;
use App\Http\Requests\Admin\CreateEmploymentRequest;
use App\Http\Requests\Admin\EmploymentCreateRequest;
use App\Http\Requests\Admin\EmploymentEditRequest;
use App\Http\Requests\Admin\EmploymentIndexRequest;
use App\Http\Requests\Admin\EmploymentShowRequest;
use App\Http\Requests\Admin\UpdateEmploymentRequest;
use App\Models\Employment;
use App\Queries\FindClientByIdQuery;
use App\Queries\FindUserByIdQuery;
use App\Queries\GetAllClientsQuery;
use App\Queries\GetAllUsersQuery;
use App\Queries\GetEmploymentByIdQuery;
use App\Queries\GetEmploymentsQuery;
use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

final readonly class EmploymentsController
{
    public function index(
        EmploymentIndexRequest $request,
        GetEmploymentsQuery $getEmploymentsQuery,
        GetAllClientsQuery $getAllClientsQuery,
    ): Response {
        $filters = EmploymentFilters::fromRequest($request);
        $employments = $getEmploymentsQuery
            ->handle($filters)
            ->withQueryString();

        return Inertia::render('admin/employments/index', [
            'employments' => fn () => $employments,
            'filters' => $request->only([
                'search',
                'sort_by',
                'sort_direction',
                'status',
                'client',
                'page',
                'per_page',
            ]),
            'clients' => $getAllClientsQuery->handle(),
        ]);
    }

    public function create(
        EmploymentCreateRequest $request,
        GetAllClientsQuery $getAllClientsQuery,
        GetAllUsersQuery $getAllUsersQuery,
    ): Response {
        return Inertia::render('admin/employments/create', [
            'clients' => $getAllClientsQuery->handle(),
            'users' => $getAllUsersQuery->handle(),
        ]);
    }

    public function store(
        CreateEmploymentRequest $request,
        CreateEmployment $createEmployment,
        FindUserByIdQuery $findUserByIdQuery,
        FindClientByIdQuery $findClientByIdQuery,
    ): RedirectResponse {
        try {
            /** @var array{
             *     user_id: int|string,
             *     client_id: int|string,
             *     position: string,
             *     hire_date: string,
             *     status: string,
             *     salary: string|null,
             *     work_location: string|null,
             *     effective_date: string,
             *     end_date: string|null
             * } $validated */
            $validated = $request->validated();

            $user = $findUserByIdQuery->handle((int) $validated['user_id']);
            $client = $findClientByIdQuery->handle((int) $validated['client_id']);

            $data = new CreateEmploymentData(
                user: $user,
                client: $client,
                position: $validated['position'],
                hireDate: $validated['hire_date'],
                status: EmploymentStatus::from($validated['status']),
                salary: $validated['salary'],
                workLocation: $validated['work_location'],
                effectiveDate: $validated['effective_date'],
                endDate: $validated['end_date'],
            );

            $createEmployment->handle($data);

            return to_route('admin.employments.index')->with('status', [
                'type' => 'success',
                'message' => __('Employment record created successfully.'),
            ]);
        } catch (Exception) {
            return back()
                ->withInput()
                ->with('status', [
                    'type' => 'error',
                    'message' => __(
                        'Failed to create employment record. Please try again.',
                    ),
                ]);
        }
    }

    public function show(
        EmploymentShowRequest $request,
        Employment $employment,
        GetEmploymentByIdQuery $getEmploymentByIdQuery,
    ): Response {
        $employment = $getEmploymentByIdQuery->handle($employment->id);

        return Inertia::render('admin/employments/show', [
            'employment' => $employment,
        ]);
    }

    public function edit(
        EmploymentEditRequest $request,
        Employment $employment,
        GetEmploymentByIdQuery $getEmploymentByIdQuery,
        GetAllClientsQuery $getAllClientsQuery,
        GetAllUsersQuery $getAllUsersQuery,
    ): Response {
        $employment = $getEmploymentByIdQuery->handle($employment->id);

        return Inertia::render('admin/employments/edit', [
            'employment' => $employment,
            'clients' => $getAllClientsQuery->handle(),
            'users' => $getAllUsersQuery->handle(),
        ]);
    }

    public function update(
        UpdateEmploymentRequest $request,
        Employment $employment,
        UpdateEmployment $updateEmployment,
        FindUserByIdQuery $findUserByIdQuery,
        FindClientByIdQuery $findClientByIdQuery,
    ): RedirectResponse {
        try {
            /** @var array{
             *     user_id: int|string,
             *     client_id: int|string,
             *     position: string,
             *     hire_date: string,
             *     status: string,
             *     salary: string|null,
             *     work_location: string|null,
             *     effective_date: string,
             *     end_date: string|null
             * } $validated */
            $validated = $request->validated();

            $user = $findUserByIdQuery->handle((int) $validated['user_id']);
            $client = $findClientByIdQuery->handle((int) $validated['client_id']);

            $data = new UpdateEmploymentData(
                employment: $employment,
                user: $user,
                client: $client,
                position: $validated['position'],
                hireDate: $validated['hire_date'],
                status: EmploymentStatus::from($validated['status']),
                salary: $validated['salary'],
                workLocation: $validated['work_location'],
                effectiveDate: $validated['effective_date'],
                endDate: $validated['end_date'],
            );

            $updateEmployment->handle($data);

            return to_route('admin.employments.index')->with('status', [
                'type' => 'success',
                'message' => __('Employment record updated successfully.'),
            ]);
        } catch (Exception) {
            return back()
                ->withInput()
                ->with('status', [
                    'type' => 'error',
                    'message' => __(
                        'Failed to update employment record. Please try again.',
                    ),
                ]);
        }
    }

    public function end(
        Employment $employment,
        EndEmployment $endEmployment,
    ): RedirectResponse {
        try {
            $endEmployment->handle($employment);

            return to_route('admin.employments.index')->with('status', [
                'type' => 'success',
                'message' => __('Employment record ended successfully.'),
            ]);
        } catch (Exception) {
            return back()->with('status', [
                'type' => 'error',
                'message' => __(
                    'Failed to end employment record. Please try again.',
                ),
            ]);
        }
    }

    public function destroy(
        Employment $employment,
        DeleteEmployment $deleteEmployment,
    ): RedirectResponse {
        try {
            $deleteEmployment->handle($employment);

            return to_route('admin.employments.index')->with('status', [
                'type' => 'success',
                'message' => __('Employment record deleted successfully.'),
            ]);
        } catch (Exception) {
            return back()->with('status', [
                'type' => 'error',
                'message' => __(
                    'Failed to delete employment record. Please try again.',
                ),
            ]);
        }
    }
}
