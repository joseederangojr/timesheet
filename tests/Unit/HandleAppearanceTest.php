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
        fn(
            $req,
        ): Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response => response(
            'OK',
        ),
    );

    expect($response->getContent())->toBe('OK');
});

it('shares appearance from cookie when set', function (): void {
    View::shouldReceive('share')->with('appearance', 'dark')->once();

    $middleware = new HandleAppearance();
    $request = Request::create('/', 'GET');
    $request->cookies->set('appearance', 'dark');

    $response = $middleware->handle(
        $request,
        fn(
            $req,
        ): Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response => response(
            'OK',
        ),
    );

    expect($response->getContent())->toBe('OK');
});

it('shares light appearance from cookie', function (): void {
    View::shouldReceive('share')->with('appearance', 'light')->once();

    $middleware = new HandleAppearance();
    $request = Request::create('/', 'GET');
    $request->cookies->set('appearance', 'light');

    $response = $middleware->handle(
        $request,
        fn(
            $req,
        ): Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response => response(
            'OK',
        ),
    );

    expect($response->getContent())->toBe('OK');
});
