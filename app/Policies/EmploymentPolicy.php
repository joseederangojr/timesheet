<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Employment;
use App\Models\User;
use App\Queries\CheckUserIsAdminQuery;
use App\Queries\CheckUserIsEmployeeQuery;

final readonly class EmploymentPolicy
{
    public function __construct(
        private CheckUserIsAdminQuery $checkUserIsAdminQuery,
        private CheckUserIsEmployeeQuery $checkUserIsEmployeeQuery,
    ) {}

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $this->isEmployee($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Employment $employment): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $this->isEmployee($user) && $employment->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Employment $employment): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Employment $employment): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Employment $employment): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Employment $employment): bool
    {
        return $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return $this->checkUserIsAdminQuery->handle($user);
    }

    private function isEmployee(User $user): bool
    {
        return $this->checkUserIsEmployeeQuery->handle($user);
    }
}
