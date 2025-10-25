<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

it('can be instantiated', function (): void {
    $role = new Role();

    expect($role)->toBeInstanceOf(Role::class);
    expect($role)->toBeInstanceOf(Model::class);
});

it('uses the correct traits', function (): void {
    $traits = class_uses(Role::class);

    expect($traits)->toContain(HasFactory::class);
});

it('has correct fillable attributes', function (): void {
    $role = new Role();

    expect($role->getFillable())->toEqual(['name', 'description']);
});

it('can be created using factory', function (): void {
    $role = Role::factory()->make();

    expect($role)->toBeInstanceOf(Role::class);
    expect($role->name)->toBeString();
    expect($role->description)->toBeString();
});

it('has users relationship', function (): void {
    $role = new Role();
    $relationship = $role->users();

    expect($relationship)->toBeInstanceOf(BelongsToMany::class);
    expect($relationship->getRelated())->toBeInstanceOf(User::class);
    expect($relationship->getTable())->toBe('user_roles');
});

it('can attach users', function (): void {
    $role = Role::factory()->create();
    $user = User::factory()->create();

    $role->users()->attach($user->id);

    expect($role->users)->toHaveCount(1);
    expect($role->users->first()->id)->toBe($user->id);
});

it('can detach users', function (): void {
    $role = Role::factory()->create();
    $user = User::factory()->create();

    $role->users()->attach($user->id);
    expect($role->users)->toHaveCount(1);

    $role->users()->detach($user->id);
    $role->refresh();

    expect($role->users)->toHaveCount(0);
});

it('can sync users', function (): void {
    $role = Role::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();

    $role->users()->attach([$user1->id, $user2->id]);
    expect($role->users)->toHaveCount(2);

    $role->users()->sync([$user2->id, $user3->id]);
    $role->refresh();

    expect($role->users)->toHaveCount(2);
    expect($role->users->pluck('id'))->toContain($user2->id, $user3->id);
    expect($role->users->pluck('id'))->not->toContain($user1->id);
});

it('has timestamps', function (): void {
    $role = Role::factory()->create();

    expect($role->created_at)->not->toBeNull();
    expect($role->updated_at)->not->toBeNull();
});

it('can be created with specific attributes', function (): void {
    $role = Role::factory()->create([
        'name' => 'Administrator',
        'description' => 'Full system access',
    ]);

    expect($role->name)->toBe('Administrator');
    expect($role->description)->toBe('Full system access');
});

it('maintains many-to-many relationship integrity', function (): void {
    $role = Role::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $role->users()->attach([$user1->id, $user2->id]);

    expect($role->users)->toHaveCount(2);
    expect($user1->roles)->toHaveCount(1);
    expect($user2->roles)->toHaveCount(1);
    expect($user1->roles->first()->id)->toBe($role->id);
    expect($user2->roles->first()->id)->toBe($role->id);
});
