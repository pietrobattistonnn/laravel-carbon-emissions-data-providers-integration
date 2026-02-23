<?php

namespace Ceedbox\EmissionsCore;

use Illuminate\Support\ServiceProvider;
use Ceedbox\EmissionsCore\Contracts\EmissionsProviderInterface;

class EmissionsCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/emissions.php',
            'emissions'
        );

        $this->app->singleton(EmissionsManager::class, function ($app) {

            $providerName = config('emissions.provider');

            $definition = config("emissions.providers.$providerName");

            if (!$definition) {
                throw new \RuntimeException("Unsupported emissions provider [$providerName]");
            }

            $providerClass = $definition['class'];
            $providerConfig = $definition['config'] ?? [];

            if (!class_exists($providerClass)) {
                throw new \RuntimeException("Provider class [$providerClass] not found");
            }

            if (!is_subclass_of($providerClass, EmissionsProviderInterface::class)) {
                throw new \RuntimeException("Provider [$providerClass] must implement EmissionsProviderInterface");
            }

            $provider = $app->make($providerClass, $providerConfig);

            return new EmissionsManager($provider);
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/emissions.php' => config_path('emissions.php'),
        ], 'emissions-config');
    }
}