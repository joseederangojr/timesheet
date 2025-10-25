<?php

declare(strict_types=1);

use App\Http\Requests\Auth\PasswordLoginRequest;

it('can be instantiated', function (): void {
    $request = new PasswordLoginRequest();

    expect($request)->toBeInstanceOf(PasswordLoginRequest::class);
});

it('always authorizes requests', function (): void {
    $request = new PasswordLoginRequest();

    expect($request->authorize())->toBeTrue();
});

it('has correct validation rules', function (): void {
    $request = new PasswordLoginRequest();
    $rules = $request->rules();

    expect($rules)->toHaveKeys(['email', 'password']);
    expect($rules['email'])->toContain('required', 'email');
    expect($rules['password'])->toContain('required', 'string');
});

it('has custom error messages', function (): void {
    $request = new PasswordLoginRequest();
    $messages = $request->messages();

    expect($messages)->toHaveKeys([
        'email.required',
        'email.email',
        'password.required',
    ]);
    expect($messages['email.required'])->toBe('Email address is required.');
    expect($messages['email.email'])->toBe(
        'Please enter a valid email address.',
    );
    expect($messages['password.required'])->toBe('Password is required.');
});
