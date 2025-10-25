<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->composerBackup = File::get(base_path('composer.json'));
});

afterEach(function () {
    File::put(base_path('composer.json'), $this->composerBackup);
});

it('shows current version', function () {
    $this->artisan('version show')
        ->expectsOutput('Current version: 0.1.0')
        ->assertExitCode(0);
});

it('can bump patch version', function () {
    $this->artisan('version bump patch')
        ->expectsOutput('Version bumped from 0.1.0 to 0.1.1')
        ->assertExitCode(0);

    $composer = json_decode(File::get(base_path('composer.json')), true);
    expect($composer['version'])->toBe('0.1.1');
});

it('can bump minor version', function () {
    $this->artisan('version bump minor')
        ->expectsOutput('Version bumped from 0.1.0 to 0.2.0')
        ->assertExitCode(0);

    $composer = json_decode(File::get(base_path('composer.json')), true);
    expect($composer['version'])->toBe('0.2.0');
});

it('can bump major version', function () {
    $this->artisan('version bump major')
        ->expectsOutput('Version bumped from 0.1.0 to 1.0.0')
        ->assertExitCode(0);

    $composer = json_decode(File::get(base_path('composer.json')), true);
    expect($composer['version'])->toBe('1.0.0');
});

it('can set specific version', function () {
    $this->artisan('version set --ver=2.5.3')
        ->expectsOutput('Version changed from 0.1.0 to 2.5.3')
        ->assertExitCode(0);

    $composer = json_decode(File::get(base_path('composer.json')), true);
    expect($composer['version'])->toBe('2.5.3');
});

it('validates semantic version format when setting', function () {
    $this->artisan('version set --ver=invalid')
        ->expectsOutput(
            'Version must follow semantic versioning format (x.y.z)',
        )
        ->assertExitCode(1);
});

it('requires version option when setting', function () {
    $this->artisan('version set')
        ->expectsOutput('Please provide a version with --ver option')
        ->assertExitCode(1);
});

it('handles invalid action', function () {
    $this->artisan('version invalid')
        ->expectsOutput(
            "Invalid action: invalid. Use 'show', 'bump', or 'set'.",
        )
        ->assertExitCode(1);
});

it('handles invalid bump type', function () {
    expect(fn() => $this->artisan('version bump invalid'))->toThrow(
        InvalidArgumentException::class,
        'Invalid version type: invalid',
    );
});
