<?php

declare(strict_types=1);

use App\Http\Middleware\HandleAppearance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

it('shares system appearance when no cookie is set', function (): void {
    View::shouldReceive('share')->with('appearance', 'system')->once();

    $middleware = new HandleAppearance();
    $request = Request::create('/', 'GET');

    $response = $middleware->handle(
        $request,
        fn (
            $req,
        ): Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response => response(
            'OK',
        ),
    );

    expect($response->getContent())->toBe('OK');
});

it('shares appearance from cookie when set', function (
    string $appearance,
): void {
    View::shouldReceive('share')->with('appearance', $appearance)->once();

    $middleware = new HandleAppearance();
    $request = Request::create('/', 'GET');
    $request->cookies->set('appearance', $appearance);

    $response = $middleware->handle(
        $request,
        fn (
            $req,
        ): Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response => response(
            'OK',
        ),
    );

    expect($response->getContent())->toBe('OK');
})->with(['dark', 'light']);
