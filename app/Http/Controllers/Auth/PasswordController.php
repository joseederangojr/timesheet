<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\PasswordLoginRequest;
use App\Models\User;
use App\Queries\CheckUserIsAdminQuery;
use App\Queries\CheckUserIsEmployeeQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

final readonly class PasswordController
{
    public function __construct(
        private CheckUserIsAdminQuery $checkUserIsAdminQuery,
        private CheckUserIsEmployeeQuery $checkUserIsEmployeeQuery,
    ) {
        //
    }

    public function authenticate(
        PasswordLoginRequest $request,
    ): RedirectResponse {
        $credentials = $request->only(['email', 'password']);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        Session::regenerate();

        /** @var User $user */
        $user = Auth::user();

        $greeting = $this->getGreetingForUser($user);

        return to_route('dashboard')->with('greeting', $greeting);
    }

    private function getGreetingForUser(User $user): string
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
