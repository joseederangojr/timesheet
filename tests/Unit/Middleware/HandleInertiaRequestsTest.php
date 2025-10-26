<?php

declare(strict_types=1);

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\User;
use Illuminate\Http\Request;

it('shares null user when guest', function (): void {
    $middleware = new HandleInertiaRequests();

    $request = Request::create('/', 'GET');

    $shared = $middleware->share($request);

    expect($shared)
        ->toHaveKey('auth')
        ->and($shared['auth'])
        ->toHaveKey('user')
        ->and($shared['auth']['user'])
        ->toBeNull()
        ->and($shared)
        ->toHaveKey('metadata')
        ->and($shared['metadata']['sidebar'])
        ->toBe(0)
        ->and($shared['metadata']['theme'])
        ->toBe('system');
});

it('shares authenticated user data', function (): void {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $middleware = new HandleInertiaRequests();

    $request = Request::create('/', 'GET');
    $request->setUserResolver(fn () => $user);

    $shared = $middleware->share($request);

    expect($shared['auth']['user'])
        ->not->toBeNull()
        ->and($shared['auth']['user']->id)
        ->toBe($user->id)
        ->and($shared['auth']['user']->name)
        ->toBe('Test User')
        ->and($shared['auth']['user']->email)
        ->toBe('test@example.com')
        ->and($shared)
        ->toHaveKey('metadata')
        ->and($shared['metadata']['sidebar'])
        ->toBe(0)
        ->and($shared['metadata']['theme'])
        ->toBe('system');
});

it('includes parent shared data', function (): void {
    $middleware = new HandleInertiaRequests();

    $request = Request::create('/', 'GET');

    $shared = $middleware->share($request);

    // Parent Inertia middleware shares 'errors' by default
    expect($shared)->toHaveKey('errors');
});

it('shares sidebar state from cookie', function (): void {
    $middleware = new HandleInertiaRequests();

    $request = Request::create('/', 'GET');
    $request->cookies->set('sidebar', '1');

    $shared = $middleware->share($request);

    expect($shared['metadata']['sidebar'])->toBe(1);
});

it('defaults sidebar state to false when cookie is missing', function (): void {
    $middleware = new HandleInertiaRequests();

    $request = Request::create('/', 'GET');

    $shared = $middleware->share($request);

    expect($shared['metadata']['sidebar'])->toBe(0);
});

it('handles false cookie value correctly', function (): void {
    $middleware = new HandleInertiaRequests();

    $request = Request::create('/', 'GET');
    $request->cookies->set('sidebar', '0');

    $shared = $middleware->share($request);

    expect($shared['metadata']['sidebar'])->toBe(0);
});

it('delegates version to parent', function (): void {
    $middleware = new HandleInertiaRequests();

    $request = Request::create('/', 'GET');

    $version = $middleware->version($request);

    // Should return parent version (which is a hash by default)
    expect($version)->toBeString();
});

it('shares theme state from theme cookie', function (): void {
    $middleware = new HandleInertiaRequests();

    $request = Request::create('/', 'GET');
    $request->cookies->set('theme', 'dark');

    $shared = $middleware->share($request);

    expect($shared['metadata']['theme'])->toBe('dark');
});

it(
    'defaults theme state to system when theme cookie is missing',
    function (): void {
        $middleware = new HandleInertiaRequests();

        $request = Request::create('/', 'GET');

        $shared = $middleware->share($request);

        expect($shared['metadata']['theme'])->toBe('system');
    },
);

it('handles light theme from theme cookie', function (): void {
    $middleware = new HandleInertiaRequests();

    $request = Request::create('/', 'GET');
    $request->cookies->set('theme', 'light');

    $shared = $middleware->share($request);

    expect($shared['metadata']['theme'])->toBe('light');
});

it('handles system theme from theme cookie', function (): void {
    $middleware = new HandleInertiaRequests();

    $request = Request::create('/', 'GET');
    $request->cookies->set('theme', 'system');

    $shared = $middleware->share($request);

    expect($shared['metadata']['theme'])->toBe('system');
});
