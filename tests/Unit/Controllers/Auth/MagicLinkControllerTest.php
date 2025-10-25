<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\MagicLinkController;
use App\Models\User;
use App\Queries\CheckUserIsAdminQuery;
use App\Queries\CheckUserIsEmployeeQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed();
});

it(
    'throws 403 exception when signature is invalid in verify method',
    function (): void {
        $checkUserIsAdminQuery = app(CheckUserIsAdminQuery::class);
        $checkUserIsEmployeeQuery = app(CheckUserIsEmployeeQuery::class);

        $controller = new MagicLinkController(
            $checkUserIsAdminQuery,
            $checkUserIsEmployeeQuery,
        );
        $user = User::factory()->create();

        // Create a request without a valid signature
        $request = Request::create('/test');

        // This should trigger the abort(403) call on line 50
        expect(
            fn(): Illuminate\Http\RedirectResponse => $controller->verify(
                $request,
                $user,
            ),
        )->toThrow(HttpException::class, 'Invalid or expired magic link.');
    },
);
