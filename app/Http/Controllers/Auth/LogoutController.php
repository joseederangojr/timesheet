<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

final class LogoutController
{
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        Session::invalidate();
        Session::regenerateToken();

        return Redirect::route('login');
    }
}
