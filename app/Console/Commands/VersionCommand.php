<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Throwable;

final class VersionCommand extends Command
{
    protected $signature = 'version {action=show : Action to perform (show, bump, set)} {type=patch : Version type (major, minor, patch)} {--ver= : Specific version to set}';

    protected $description = 'Manage application semantic versioning';

    public function handle(): int
    {
        $action = $this->argument('action');

        return match ($action) {
            'show' => $this->showVersion(),
            'bump' => $this->bumpVersion(),
            'set' => $this->setVersion(),
            default => (function () use ($action): int {
                $this->error(
                    sprintf(
                        "Invalid action: %s. Use 'show', 'bump', or 'set'.",
                        $action,
                    ),
                );

                return 1;
            })(),
        };
    }

    private function showVersion(): int
    {
        $version = $this->getCurrentVersion();
        $this->info('Current version: ' . $version);

        return 0;
    }

    private function bumpVersion(): int
    {
        $type = $this->argument('type');
        $currentVersion = $this->getCurrentVersion();

        if (!preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $currentVersion, $matches)) {
            $this->error('Invalid current version format in composer.json');

            return 1;
        }

        [$_, $major, $minor, $patch] = $matches;

        $newVersion = match ($type) {
            'major' => $major + 1 . '.0.0',
            'minor' => $major . '.' . ($minor + 1) . '.0',
            'patch' => $major . '.' . $minor . '.' . ($patch + 1),
            default => throw new InvalidArgumentException(
                'Invalid version type: ' . $type,
            ),
        };

        $this->updateComposerVersion($newVersion);
        $this->info(
            sprintf(
                'Version bumped from %s to %s',
                $currentVersion,
                $newVersion,
            ),
        );

        return 0;
    }

    private function setVersion(): int
    {
        $version = $this->option('ver');

        if (!$version) {
            $this->error('Please provide a version with --ver option');

            return 1;
        }

        if (!preg_match('/^\d+\.\d+\.\d+$/', $version)) {
            $this->error(
                'Version must follow semantic versioning format (x.y.z)',
            );

            return 1;
        }

        $currentVersion = $this->getCurrentVersion();
        $this->updateComposerVersion($version);
        $this->info(
            sprintf('Version changed from %s to %s', $currentVersion, $version),
        );

        return 0;
    }

    private function getCurrentVersion(): string
    {
        $composerPath = base_path('composer.json');

        try {
            $composerContent = file_get_contents($composerPath);
        } catch (Throwable) {
            return '0.0.0';
        }

        $composer = json_decode($composerContent, true);

        if (!is_array($composer)) {
            return '0.0.0';
        }

        $version = $composer['version'] ?? '0.0.0';

        return is_string($version) ? $version : '0.0.0';
    }

    private function updateComposerVersion(string $version): void
    {
        $composerPath = base_path('composer.json');

        try {
            $composerContent = file_get_contents($composerPath);
        } catch (Throwable) {
            return;
        }

        $composer = json_decode($composerContent, true);

        if (!is_array($composer)) {
            return;
        }

        $composer['version'] = $version;

        file_put_contents(
            $composerPath,
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) .
                "\n",
        );
    }
}
