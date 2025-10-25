<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\PasswordLoginRequest;
use App\Models\User;
use App\Queries\CheckUserIsAdminQuery;
use App\Queries\CheckUserIsEmployeeQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

final class PasswordController
{
    public function __construct(
        private readonly CheckUserIsAdminQuery $checkUserIsAdminQuery,
        private readonly CheckUserIsEmployeeQuery $checkUserIsEmployeeQuery,
    ) {
        //
    }

    public function authenticate(
        PasswordLoginRequest $request,
    ): RedirectResponse {
        $credentials = $request->only(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return Redirect::back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        Session::regenerate();

        $user = Auth::user();
        $greeting = $this->getGreetingForUser($user);

        return Redirect::route('dashboard')->with('greeting', $greeting);
    }

    private function getGreetingForUser(User $user): string
    {
        if ($this->checkUserIsAdminQuery->handle($user)) {
            return "Hello, {$user->name}";
        }

        if ($this->checkUserIsEmployeeQuery->handle($user)) {
            return "Hi, {$user->name}";
        }

        return "Welcome, {$user->name}";
    }
}
