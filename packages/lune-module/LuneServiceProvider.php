<?php

namespace Ceedbox\LuneModule;

use Illuminate\Support\ServiceProvider;
use Ceedbox\EmissionsCore\Contracts\EmissionsProviderInterface;

class LuneServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/lune.php',
            'lune'
        );

        $this->app->bind(LuneClient::class, function () {
            return new LuneClient(
                orgId: config('lune.org_id'),
                apiKey: config('lune.api_key'),
                baseUrl: config('lune.base_url'),
                ttl: config('lune.ttl')
            );
        });

        // Bind contract automatically
        $this->app->bind(EmissionsProviderInterface::class, function ($app) {
            if (config('emissions.provider') === 'lune') {
                return $app->make(LuneClient::class);
            }

            return null;
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/lune.php' => config_path('lune.php'),
        ], 'emissions-config');
    }
}