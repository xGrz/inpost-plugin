<?php

namespace Xgrz\InPost;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Xgrz\InPost\Console\Commands\ConfigCommand;
use Xgrz\InPost\Console\Commands\PointsCommand;
use Xgrz\InPost\Console\Commands\PublishConfigCommand;
use Xgrz\InPost\Console\Commands\PublishMigrationsCommand;
use Xgrz\InPost\Http\Middleware\IpAddressRestrictionMiddleware;

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
        $this->commands([
            PublishConfigCommand::class,
            PublishMigrationsCommand::class,
            ConfigCommand::class,
            PointsCommand::class,
        ]);

        $this->publishes(
            [__DIR__ . '/../config/inpost.php' => config_path('inpost.php')],
            'inpost-config'
        );
        Route::aliasMiddleware('inpost-ip-restriction', IpAddressRestrictionMiddleware::class);
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
