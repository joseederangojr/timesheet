<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    expect($user->getFillable())->toEqual(['name', 'email', 'password']);
});

it('has correct hidden attributes', function (): void {
    $user = new User();

    expect($user->getHidden())->toEqual(['password', 'remember_token']);
});

it('has correct casts', function (): void {
    $user = new User();

    expect($user->getCasts())->toHaveKeys(['email_verified_at', 'password']);

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

it('has roles relationship', function (): void {
    $user = new User();
    $relationship = $user->roles();

    expect($relationship)->toBeInstanceOf(BelongsToMany::class);
    expect($relationship->getRelated())->toBeInstanceOf(Role::class);
    expect($relationship->getTable())->toBe('user_roles');
});

it('can attach roles', function (): void {
    $user = User::factory()->create();
    $role = Role::factory()->create();

    $user->roles()->attach($role->id);

    expect($user->roles)->toHaveCount(1);
    expect($user->roles->first()->id)->toBe($role->id);
});

it('can detach roles', function (): void {
    $user = User::factory()->create();
    $role = Role::factory()->create();

    $user->roles()->attach($role->id);
    expect($user->roles)->toHaveCount(1);

    $user->roles()->detach($role->id);
    $user->refresh();

    expect($user->roles)->toHaveCount(0);
});

it('can sync roles', function (): void {
    $user = User::factory()->create();
    $role1 = Role::factory()->create();
    $role2 = Role::factory()->create();
    $role3 = Role::factory()->create();

    $user->roles()->attach([$role1->id, $role2->id]);
    expect($user->roles)->toHaveCount(2);

    $user->roles()->sync([$role2->id, $role3->id]);
    $user->refresh();

    expect($user->roles)->toHaveCount(2);
    expect($user->roles->pluck('id'))->toContain($role2->id, $role3->id);
    expect($user->roles->pluck('id'))->not->toContain($role1->id);
});

it('password is hashed when set', function (): void {
    $user = User::factory()->make(['password' => 'plain-password']);

    expect($user->password)->not->toBe('plain-password');
    expect(mb_strlen((string) $user->password))->toBeGreaterThan(50);
});
