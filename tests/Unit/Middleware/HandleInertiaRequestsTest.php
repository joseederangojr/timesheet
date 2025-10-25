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
        ->toBeNull();
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
        ->toBe('test@example.com');
});

it('includes parent shared data', function (): void {
    $middleware = new HandleInertiaRequests();

    $request = Request::create('/', 'GET');

    $shared = $middleware->share($request);

    // Parent Inertia middleware shares 'errors' by default
    expect($shared)->toHaveKey('errors');
});

it('delegates version to parent', function (): void {
    $middleware = new HandleInertiaRequests();

    $request = Request::create('/', 'GET');

    $version = $middleware->version($request);

    // Should return parent version (which is a hash by default)
    expect($version)->toBeString();
});
