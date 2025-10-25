<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\MagicLinkRequest;
use App\Models\User;
use App\Notifications\Auth\MagicLinkNotification;
use App\Queries\CheckUserIsAdminQuery;
use App\Queries\CheckUserIsEmployeeQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

final readonly class MagicLinkController
{
    public function __construct(
        private CheckUserIsAdminQuery $checkUserIsAdminQuery,
        private CheckUserIsEmployeeQuery $checkUserIsEmployeeQuery,
    ) {
        //
    }

    public function sendMagicLink(MagicLinkRequest $request): RedirectResponse
    {
        $user = User::query()
            ->where('email', $request->validated('email'))
            ->firstOrFail();

        $magicLinkUrl = URL::temporarySignedRoute(
            'login.magic-link.verify',
            now()->addMinutes(15),
            ['user' => $user->id],
        );

        $user->notify(new MagicLinkNotification($magicLinkUrl));

        return back()->with(
            'message',
            "We've sent a magic link to your email address. Please check your inbox.",
        );
    }

    public function verify(Request $request, User $user): RedirectResponse
    {
        abort_unless(
            $request->hasValidSignature() === true,
            403,
            'Invalid or expired magic link.',
        );

        Auth::login($user);

        $greeting = $this->getGreetingForUser($user);

        if ($this->checkUserIsAdminQuery->handle($user)) {
            return to_route('admin.dashboard');
        }

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
