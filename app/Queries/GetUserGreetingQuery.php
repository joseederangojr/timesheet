<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;

final readonly class GetUserGreetingQuery
{
    public function __construct(
        private CheckUserIsAdminQuery $checkUserIsAdminQuery,
        private CheckUserIsEmployeeQuery $checkUserIsEmployeeQuery,
    ) {
        //
    }

    public function handle(User $user): string
    {
        if ($this->checkUserIsAdminQuery->handle($user)) {
            return 'Hello, '.$user->name;
        }

        if ($this->checkUserIsEmployeeQuery->handle($user)) {
            return 'Hi, '.$user->name;
        }

        return 'Welcome, '.$user->name;
    }
}
