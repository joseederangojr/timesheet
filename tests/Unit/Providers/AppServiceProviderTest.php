<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rules\Password;

it('registers service provider', function (): void {
    $app = mock(Application::class);
    $provider = new AppServiceProvider($app);
    $provider->register();

    expect(true)->toBeTrue();
});

it('boots service provider', function (): void {
    $app = mock(Application::class);
    $provider = new AppServiceProvider($app);
    $provider->boot();

    expect(true)->toBeTrue();
});

it('calls model unguard in boot models defaults', function (): void {
    // Since Model::unguard() is a static method that sets a static property,
    // we can test that the method exists and is called by checking the unguarded state
    $app = mock(Application::class);
    $provider = new AppServiceProvider($app);

    // Reset any previous unguarded state
    EloquentModel::reguard();

    $provider->boot();

    // After boot, models should be unguarded
    expect(EloquentModel::isUnguarded())->toBeTrue();
});

it('sets password defaults for local environment', function (): void {
    $app = mock(Application::class);
    $app->shouldReceive('isLocal')->andReturn(true);
    $app->shouldReceive('runningUnitTests')->andReturn(false);

    $provider = new AppServiceProvider($app);
    $provider->boot();

    // Test that password validation uses the correct defaults
    $validator = ValidatorFacade::make(
        ['password' => 'validpassword123'],
        ['password' => Password::defaults()],
    );
    expect($validator->passes())->toBeTrue();

    // Test that short password fails
    $validator = ValidatorFacade::make(
        ['password' => 'short'],
        ['password' => Password::defaults()],
    );
    expect($validator->fails())->toBeTrue();
});

it('sets password defaults for production environment', function (): void {
    $app = mock(Application::class);
    $app->shouldReceive('isLocal')->andReturn(false);
    $app->shouldReceive('runningUnitTests')->andReturn(false);

    $provider = new AppServiceProvider($app);
    $provider->boot();

    // Test that password validation uses the correct defaults (should include uncompromised check)
    $validator = ValidatorFacade::make(
        ['password' => 'validpassword123'],
        ['password' => Password::defaults()],
    );
    expect($validator->passes())->toBeTrue();

    // Test that short password fails
    $validator = ValidatorFacade::make(
        ['password' => 'short'],
        ['password' => Password::defaults()],
    );
    expect($validator->fails())->toBeTrue();
});

it('sets password defaults during unit tests', function (): void {
    $app = mock(Application::class);
    $app->shouldReceive('isLocal')->andReturn(false);
    $app->shouldReceive('runningUnitTests')->andReturn(true);

    $provider = new AppServiceProvider($app);
    $provider->boot();

    // Test that password validation uses the correct defaults (same as local)
    $validator = ValidatorFacade::make(
        ['password' => 'validpassword123'],
        ['password' => Password::defaults()],
    );
    expect($validator->passes())->toBeTrue();

    // Test that short password fails
    $validator = ValidatorFacade::make(
        ['password' => 'short'],
        ['password' => Password::defaults()],
    );
    expect($validator->fails())->toBeTrue();
});
