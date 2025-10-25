<?php

declare(strict_types=1);

use App\Services\Boost\OpenCode;
use Laravel\Boost\Install\Detection\DetectionStrategyFactory;
use Laravel\Boost\Install\Enums\McpInstallationStrategy;
use Laravel\Boost\Install\Enums\Platform;

it('returns correct name', function (): void {
    $factory = mock(DetectionStrategyFactory::class);
    $openCode = new OpenCode($factory);

    expect($openCode->name())->toBe('opencode');
});

it('returns correct display name', function (): void {
    $factory = mock(DetectionStrategyFactory::class);
    $openCode = new OpenCode($factory);

    expect($openCode->displayName())->toBe('OpenCode');
});

it('returns system detection config for darwin', function (): void {
    $factory = mock(DetectionStrategyFactory::class);
    $openCode = new OpenCode($factory);

    $config = $openCode->systemDetectionConfig(Platform::Darwin);

    expect($config)->toBe([
        'command' => 'command -v opencode',
    ]);
});

it('returns system detection config for linux', function (): void {
    $factory = mock(DetectionStrategyFactory::class);
    $openCode = new OpenCode($factory);

    $config = $openCode->systemDetectionConfig(Platform::Linux);

    expect($config)->toBe([
        'command' => 'command -v opencode',
    ]);
});

it('returns system detection config for windows', function (): void {
    $factory = mock(DetectionStrategyFactory::class);
    $openCode = new OpenCode($factory);

    $config = $openCode->systemDetectionConfig(Platform::Windows);

    expect($config)->toBe([
        'command' => 'where opencode 2>nul',
    ]);
});

it('returns project detection config', function (): void {
    $factory = mock(DetectionStrategyFactory::class);
    $openCode = new OpenCode($factory);

    $config = $openCode->projectDetectionConfig();

    expect($config)->toBe([
        'files' => ['AGENTS.md', 'opencode.json'],
    ]);
});

it('returns mcp installation strategy', function (): void {
    $factory = mock(DetectionStrategyFactory::class);
    $openCode = new OpenCode($factory);

    expect($openCode->mcpInstallationStrategy())->toBe(McpInstallationStrategy::FILE);
});

it('returns mcp config path', function (): void {
    $factory = mock(DetectionStrategyFactory::class);
    $openCode = new OpenCode($factory);

    expect($openCode->mcpConfigPath())->toBe('opencode.json');
});

it('returns guidelines path', function (): void {
    $factory = mock(DetectionStrategyFactory::class);
    $openCode = new OpenCode($factory);

    expect($openCode->guidelinesPath())->toBe('AGENTS.md');
});

it('returns mcp config key', function (): void {
    $factory = mock(DetectionStrategyFactory::class);
    $openCode = new OpenCode($factory);

    expect($openCode->mcpConfigKey())->toBe('mcp');
});

it('returns default mcp config', function (): void {
    $factory = mock(DetectionStrategyFactory::class);
    $openCode = new OpenCode($factory);

    $config = $openCode->defaultMcpConfig();

    expect($config)->toBe([
        '$schema' => 'https://opencode.ai/config.json',
    ]);
});

it('returns mcp server config', function (): void {
    $factory = mock(DetectionStrategyFactory::class);
    $openCode = new OpenCode($factory);

    $config = $openCode->mcpServerConfig('opencode', ['--arg'], ['ENV_VAR' => 'value']);

    expect($config)->toBe([
        'type' => 'local',
        'enabled' => true,
        'command' => ['opencode', '--arg'],
        'environment' => ['ENV_VAR' => 'value'],
    ]);
});

it('returns mcp server config with no args or env', function (): void {
    $factory = mock(DetectionStrategyFactory::class);
    $openCode = new OpenCode($factory);

    $config = $openCode->mcpServerConfig('opencode');

    expect($config)->toBe([
        'type' => 'local',
        'enabled' => true,
        'command' => ['opencode'],
        'environment' => [],
    ]);
});
