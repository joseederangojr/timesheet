<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\PasswordLoginRequest;
use App\Models\User;
use App\Queries\CheckUserIsAdminQuery;
use App\Queries\GetUserGreetingQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

final readonly class PasswordController
{
    public function __construct(
        private CheckUserIsAdminQuery $checkUserIsAdminQuery,
        private GetUserGreetingQuery $getUserGreetingQuery,
    ) {
        //
    }

    public function store(PasswordLoginRequest $request): RedirectResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        Session::regenerate();

        /** @var User $user */
        $user = Auth::user();

        $greeting = $this->getUserGreetingQuery->handle($user);

        if ($this->checkUserIsAdminQuery->handle($user)) {
            return to_route('admin.dashboard')->with('greeting', $greeting);
        }

        return to_route('dashboard')->with('greeting', $greeting);
    }
}
