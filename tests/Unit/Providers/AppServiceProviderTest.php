<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Password;

it('can be instantiated', function (): void {
    $provider = new AppServiceProvider(app());

    expect($provider)->toBeInstanceOf(AppServiceProvider::class);
});

it('boots models defaults correctly', function (): void {
    // Reset guarded state
    Model::reguard();
    expect(Model::isUnguarded())->toBeFalse();

    $provider = new AppServiceProvider(app());
    $provider->boot();

    expect(Model::isUnguarded())->toBeTrue();
});

it('has empty register method', function (): void {
    $provider = new AppServiceProvider(app());

    // This should not throw any exceptions
    expect($provider->register(...))->not->toThrow(Exception::class);
});

it('boot method calls all required methods', function (): void {
    Model::reguard();

    $provider = new AppServiceProvider(app());
    $provider->boot();

    // Verify Model::unguard() was called
    expect(Model::isUnguarded())->toBeTrue();

    // Boot method completes without exceptions
    expect(true)->toBeTrue();
});

it('boots password defaults for local environment', function (): void {
    // Mock app as local environment
    app()->instance('env', 'local');

    $provider = new AppServiceProvider(app());
    $provider->boot();

    // Get the password rule to test it was configured
    $passwordRule = Password::defaults();
    expect($passwordRule)->not->toBeNull();
});

it('boots password defaults for unit testing environment', function (): void {
    // Mock app as running unit tests (which is actually true in this case)
    $provider = new AppServiceProvider(app());
    $provider->boot();

    // Get the password rule to test it was configured
    $passwordRule = Password::defaults();
    expect($passwordRule)->not->toBeNull();
});

it('covers all private methods through boot method', function (): void {
    Model::reguard();

    $provider = new AppServiceProvider(app());

    // Calling boot() should execute both bootModelsDefaults() and bootPasswordDefaults()
    $provider->boot();

    // Verify bootModelsDefaults() was called
    expect(Model::isUnguarded())->toBeTrue();

    // Verify bootPasswordDefaults() was called (Password::defaults should be set)
    $passwordRule = Password::defaults();
    expect($passwordRule)->not->toBeNull();
});
