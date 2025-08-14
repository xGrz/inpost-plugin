<?php

namespace Xgrz\InPost;

use Illuminate\Support\ServiceProvider;

class InPostServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        self::setupMigrations();
        self::setupShipXRouting();
        self::setupTranslations();
    }

    public function boot()
    {
        $this->publishes(
            [__DIR__ . '/../config/inpost.php' => config_path('inpost.php')],
            'inpost-config'
        );
    }

    private function setupMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/migrations' => $this->app->databasePath('migrations'),
            ], 'inpost-migrations');
        }
    }

    private function setupShipXRouting(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }

    private function setupTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'inpost');
    }

}
