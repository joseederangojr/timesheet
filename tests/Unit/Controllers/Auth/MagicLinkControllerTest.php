<?php

declare(strict_types=1);

use App\Actions\FindUserByEmail;
use App\Http\Controllers\Auth\MagicLinkController;
use App\Models\User;
use App\Queries\CheckUserIsAdminQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed();
});

it(
    'throws 403 exception when signature is invalid in show method',
    function (): void {
        $findUserByEmailAction = app(FindUserByEmail::class);
        $checkUserIsAdminQuery = app(CheckUserIsAdminQuery::class);
        $getUserGreetingQuery = app(App\Queries\GetUserGreetingQuery::class);

        $controller = new MagicLinkController(
            $findUserByEmailAction,
            $checkUserIsAdminQuery,
            $getUserGreetingQuery,
        );
        $user = User::factory()->create();

        // Create a request without a valid signature
        $request = Request::create('/test');

        // This should trigger the abort(403) call on line 50
        expect(
            fn (): Illuminate\Http\RedirectResponse => $controller->show(
                $request,
                $user,
            ),
        )->toThrow(HttpException::class, 'Invalid or expired magic link.');
    },
);
