<?php

namespace Otparya\OtpArya;

use Illuminate\Support\ServiceProvider;

class OtpAryaServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge configuration file
        $this->mergeConfigFrom(
            __DIR__ . '/../config/otp-arya.php',
            'otp-arya'
        );
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // Publish configuration file
        $this->publishes([
            __DIR__ . '/../config/otp-arya.php' => config_path('otp-arya.php'),
        ], 'config');
    }
}
