<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

function mockComposerContent(string $version = '1.0.0'): string
{
    return json_encode(
        [
            'name' => 'test/app',
            'version' => $version,
            'description' => 'Test application',
        ],
        JSON_PRETTY_PRINT,
    );
}

it('shows current version', function (): void {
    File::shouldReceive('exists')
        ->with(base_path('composer.json'))
        ->andReturn(true);

    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(mockComposerContent());

    $this->artisan('version show')
        ->expectsOutput('Current version: 1.0.0')
        ->assertExitCode(0);
});

it('can bump version', function (string $type, string $expected): void {
    File::shouldReceive('exists')
        ->with(base_path('composer.json'))
        ->andReturn(true);

    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(mockComposerContent());

    File::shouldReceive('put')
        ->with(base_path('composer.json'), Mockery::any())
        ->once();

    $this->artisan('version bump '.$type)
        ->expectsOutput('Version bumped from 1.0.0 to '.$expected)
        ->assertExitCode(0);
})->with([
    'patch' => ['patch', '1.0.1'],
    'minor' => ['minor', '1.1.0'],
    'major' => ['major', '2.0.0'],
]);

it('can set specific version', function (): void {
    File::shouldReceive('exists')
        ->with(base_path('composer.json'))
        ->andReturn(true);

    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(mockComposerContent());

    File::shouldReceive('put')
        ->with(base_path('composer.json'), Mockery::any())
        ->once();

    $this->artisan('version set --ver=2.5.3')
        ->expectsOutput('Version changed from 1.0.0 to 2.5.3')
        ->assertExitCode(0);
});

it('validates semantic version format when setting', function (): void {
    $this->artisan('version set --ver=invalid')
        ->expectsOutput(
            'Version must follow semantic versioning format (x.y.z)',
        )
        ->assertExitCode(1);
});

it('requires version option when setting', function (): void {
    $this->artisan('version set')
        ->expectsOutput('Please provide a version with --ver option')
        ->assertExitCode(1);
});

it('handles invalid version format in composer.json', function (): void {
    File::shouldReceive('exists')
        ->with(base_path('composer.json'))
        ->andReturn(true);

    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(mockComposerContent('invalid-version'));

    $this->artisan('version bump patch')
        ->expectsOutput('Invalid current version format in composer.json')
        ->assertExitCode(1);
});

it('handles invalid action', function (): void {
    $this->artisan('version invalid-action')
        ->expectsOutput(
            "Invalid action: invalid-action. Use 'show', 'bump', or 'set'.",
        )
        ->assertExitCode(1);
});

it('handles invalid bump type', function (): void {
    File::shouldReceive('exists')
        ->with(base_path('composer.json'))
        ->andReturn(true);

    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(mockComposerContent());

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Invalid version type: invalid-type');

    $this->artisan('version bump invalid-type');
});

it(
    'returns default version when composer.json does not exist',
    function (): void {
        File::shouldReceive('exists')
            ->with(base_path('composer.json'))
            ->andReturn(false);

        $this->artisan('version show')
            ->expectsOutput('Current version: 0.0.0')
            ->assertExitCode(0);
    },
);

it(
    'returns default version when composer.json has no version field',
    function (): void {
        File::shouldReceive('exists')
            ->with(base_path('composer.json'))
            ->andReturn(true);

        File::shouldReceive('get')
            ->with(base_path('composer.json'))
            ->andReturn(json_encode(['name' => 'test/app'], JSON_PRETTY_PRINT));

        $this->artisan('version show')
            ->expectsOutput('Current version: 0.0.0')
            ->assertExitCode(0);
    },
);

it(
    'returns default version when composer.json has invalid json',
    function (): void {
        File::shouldReceive('exists')
            ->with(base_path('composer.json'))
            ->andReturn(true);

        File::shouldReceive('get')
            ->with(base_path('composer.json'))
            ->andReturn('{invalid json}');

        $this->artisan('version show')
            ->expectsOutput('Current version: 0.0.0')
            ->assertExitCode(0);
    },
);

it(
    'handles file write when composer.json does not exist during update',
    function (): void {
        File::shouldReceive('exists')
            ->with(base_path('composer.json'))
            ->andReturn(false)
            ->twice(); // Once for getCurrentVersion, once for updateComposerVersion

        $this->artisan('version set --ver=1.2.3')
            ->expectsOutput('Version changed from 0.0.0 to 1.2.3')
            ->assertExitCode(0);
    },
);

it('handles json decode error during update silently', function (): void {
    File::shouldReceive('exists')
        ->with(base_path('composer.json'))
        ->andReturn(true);

    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn(mockComposerContent())
        ->once(); // For getCurrentVersion

    File::shouldReceive('get')
        ->with(base_path('composer.json'))
        ->andReturn('{invalid json}')
        ->once(); // For updateComposerVersion

    $this->artisan('version set --ver=1.2.3')
        ->expectsOutput('Version changed from 1.0.0 to 1.2.3')
        ->assertExitCode(0);
});
