<?php

declare(strict_types=1);

it('can get version from config', function () {
    $version = config('app.version');

    expect($version)->toBeString();
    expect($version)->toMatch('/^\d+\.\d+\.\d+$/');
});

it('can get version using helper function', function () {
    $version = app_version();

    expect($version)->toBeString();
    expect($version)->toMatch('/^\d+\.\d+\.\d+$/');
    expect($version)->toBe(config('app.version'));
});

it('returns default version when composer.json is missing', function () {
    $originalPath = base_path('composer.json');
    $backupPath = base_path('composer.json.backup');

    rename($originalPath, $backupPath);

    try {
        $config = app('config');
        $config->set('app.version', '0.0.0');

        expect(config('app.version'))->toBe('0.0.0');
    } finally {
        rename($backupPath, $originalPath);
    }
});
