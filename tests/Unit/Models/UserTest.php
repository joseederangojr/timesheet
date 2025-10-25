<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Auth;
use Illuminate\Notifications\Notifiable;

it('can be instantiated', function (): void {
    $user = new User();

    expect($user)->toBeInstanceOf(User::class);
    expect($user)->toBeInstanceOf(Auth::class);
});

it('uses the correct traits', function (): void {
    $traits = class_uses(User::class);

    expect($traits)->toContain(HasFactory::class);
    expect($traits)->toContain(Notifiable::class);
});

it('has correct fillable attributes', function (): void {
    $user = new User();

    expect($user->getFillable())->toEqual([
        'name',
        'email',
        'password',
    ]);
});

it('has correct hidden attributes', function (): void {
    $user = new User();

    expect($user->getHidden())->toEqual([
        'password',
        'remember_token',
    ]);
});

it('has correct casts', function (): void {
    $user = new User();

    expect($user->getCasts())->toHaveKeys([
        'email_verified_at',
        'password',
    ]);

    expect($user->getCasts()['email_verified_at'])->toBe('datetime');
    expect($user->getCasts()['password'])->toBe('hashed');
});

it('can be created using factory', function (): void {
    $user = User::factory()->make();

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBeString();
    expect($user->email)->toBeString();
    expect($user->password)->toBeString();
});
