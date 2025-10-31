<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\FindUserByEmail;
use App\Http\Requests\Auth\MagicLinkRequest;
use App\Models\User;
use App\Notifications\Auth\MagicLinkNotification;
use App\Queries\CheckUserIsAdminQuery;
use App\Queries\GetUserGreetingQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

final readonly class MagicLinkController
{
    public function __construct(
        private FindUserByEmail $findUserByEmailAction,
        private CheckUserIsAdminQuery $checkUserIsAdminQuery,
        private GetUserGreetingQuery $getUserGreetingQuery,
    ) {
        //
    }

    public function store(MagicLinkRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        /** @var string $email */
        $email = $validated['email'];
        $user = $this->findUserByEmailAction->handle($email);

        $magicLinkUrl = URL::temporarySignedRoute(
            'auth.magic-link.show',
            now()->addMinutes(15),
            ['user' => $user->id],
        );

        $user->notify(new MagicLinkNotification($magicLinkUrl));

        return back()->with('status', [
            'type' => 'info',
            'message' => "We've sent a magic link to your email address. Please check your inbox.",
        ]);
    }

    public function show(Request $request, User $user): RedirectResponse
    {
        abort_unless(
            $request->hasValidSignature() === true,
            403,
            'Invalid or expired magic link.',
        );

        Auth::login($user);

        $greeting = $this->getUserGreetingQuery->handle($user);

        if ($this->checkUserIsAdminQuery->handle($user)) {
            return to_route('admin.dashboard')->with('status', [
                'type' => 'success',
                'message' => $greeting,
            ]);
        }

        return to_route('dashboard')->with('status', [
            'type' => 'success',
            'message' => $greeting,
        ]);
    }
}
