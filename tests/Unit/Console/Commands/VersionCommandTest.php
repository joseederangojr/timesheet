<?php

declare(strict_types=1);

use App\Console\Commands\VersionCommand;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    $this->composerBackup = File::get(base_path('composer.json'));
});

afterEach(function (): void {
    if (File::exists(base_path('composer.json'))) {
        File::put(base_path('composer.json'), $this->composerBackup);
    } else {
        File::put(base_path('composer.json'), $this->composerBackup);
    }
});

it(
    'handles missing composer.json file in version command methods',
    function (): void {
        $command = new VersionCommand();

        // Delete composer.json temporarily
        $composerPath = base_path('composer.json');
        File::delete($composerPath);

        try {
            // Test getCurrentVersion - should return '0.0.0' when file doesn't exist
            $reflection = new ReflectionClass($command);
            $method = $reflection->getMethod('getCurrentVersion');
            $method->setAccessible(true);

            $result = $method->invoke($command);
            expect($result)->toBe('0.0.0');

            // Test updateComposerVersion - should handle missing file gracefully
            $updateMethod = $reflection->getMethod('updateComposerVersion');
            $updateMethod->setAccessible(true);

            // This should not throw an exception and should handle the missing file
            $updateMethod->invoke($command, '1.0.0');

            // File should still not exist
            expect(File::exists($composerPath))->toBeFalse();
        } finally {
            // Restore the file if it doesn't exist
            if (!File::exists($composerPath)) {
                // Test will restore in afterEach
            }
        }
    },
);

it('handles invalid json in composer.json file', function (): void {
    $command = new VersionCommand();

    // Create invalid JSON
    $composerPath = base_path('composer.json');
    File::put($composerPath, 'invalid json content');

    // Test getCurrentVersion - should return '0.0.0' when json_decode fails
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('getCurrentVersion');
    $method->setAccessible(true);

    $result = $method->invoke($command);
    expect($result)->toBe('0.0.0');

    // Test updateComposerVersion - should handle invalid JSON gracefully
    $updateMethod = $reflection->getMethod('updateComposerVersion');
    $updateMethod->setAccessible(true);

    // This should not throw an exception and should handle the invalid JSON
    $updateMethod->invoke($command, '1.0.0');

    // The file should remain unchanged with invalid JSON content
    expect(File::get($composerPath))->toBe('invalid json content');
});
