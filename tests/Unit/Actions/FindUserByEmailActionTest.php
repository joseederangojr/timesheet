<?php

declare(strict_types=1);

use App\Actions\FindUserByEmail;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->action = app(FindUserByEmail::class);
});

it('finds user by email successfully', function (): void {
    $user = User::factory()->create(['email' => 'test@example.com']);

    $foundUser = $this->action->handle('test@example.com');

    expect($foundUser)->toBeInstanceOf(User::class);
    expect($foundUser->id)->toBe($user->id);
    expect($foundUser->email)->toBe('test@example.com');
});

it('throws exception when user email does not exist', function (): void {
    expect(
        fn (): User => $this->action->handle('nonexistent@example.com'),
    )->toThrow(ModelNotFoundException::class);
});

it('returns exact user when multiple users exist', function (): void {
    User::factory()->create(['email' => 'other@example.com']);
    $targetUser = User::factory()->create(['email' => 'target@example.com']);
    User::factory()->create(['email' => 'another@example.com']);

    $foundUser = $this->action->handle('target@example.com');

    expect($foundUser->id)->toBe($targetUser->id);
    expect($foundUser->email)->toBe('target@example.com');
});
