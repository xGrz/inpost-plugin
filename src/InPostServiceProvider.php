<?php

namespace Xgrz\InPost;

use Illuminate\Support\ServiceProvider;

class InPostServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        self::setupMigrations();
        self::setupNotificationRouting();
        self::setupTranslations();
    }

    private function setupMigrations(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    private function setupNotificationRouting(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }

    private function setupTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'inpost');
    }

}
