<?php

declare(strict_types=1);

use App\Http\Requests\Auth\MagicLinkRequest;

it('can be instantiated', function (): void {
    $request = new MagicLinkRequest();

    expect($request)->toBeInstanceOf(MagicLinkRequest::class);
});

it('always authorizes requests', function (): void {
    $request = new MagicLinkRequest();

    expect($request->authorize())->toBeTrue();
});

it('has correct validation rules', function (): void {
    $request = new MagicLinkRequest();
    $rules = $request->rules();

    expect($rules)->toHaveKey('email');
    expect($rules['email'])->toContain(
        'required',
        'email',
        'exists:users,email',
    );
});

it('has custom error messages', function (): void {
    $request = new MagicLinkRequest();
    $messages = $request->messages();

    expect($messages)->toHaveKeys([
        'email.required',
        'email.email',
        'email.exists',
    ]);
    expect($messages['email.required'])->toBe('Email address is required.');
    expect($messages['email.email'])->toBe(
        'Please enter a valid email address.',
    );
    expect($messages['email.exists'])->toBe(
        "We couldn't find an account with that email address.",
    );
});
