<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Boost\OpenCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Boost\Boost;
use Laravel\Boost\BoostManager;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->bootModelsDefaults();
        $this->bootPasswordDefaults();
        $this->bootBoostDefaults();
    }

    private function bootModelsDefaults(): void
    {
        Model::unguard();
    }

    private function bootPasswordDefaults(): void
    {
        Password::defaults(fn () => app()->isLocal() || app()->runningUnitTests() ? Password::min(12)->max(255) : Password::min(12)->max(255)->uncompromised());
    }

    private function bootBoostDefaults(): void
    {
        $manager = app(BoostManager::class);
        $environments = $manager->getCodeEnvironments();

        if (! array_key_exists('opencode', $environments)) {
            Boost::registerCodeEnvironment('opencode', OpenCode::class);
        }
    }
}
