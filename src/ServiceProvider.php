<?php

declare(strict_types=1);

namespace Appleton\Threads;

use Appleton\Threads\Console\Commands\UnblockCommenterCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/threads.php', 'threads');

        $this->app->register(Providers\EventServiceProvider::class);
        $this->app->register(Providers\RouteServiceProvider::class);

        $this->publishes([
            __DIR__.'/../config/threads.php' => config_path('threads.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'threads-migrations');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->commands([
            UnblockCommenterCommand::class,
        ]);

        $this->app->booted(function () {
            $this->app->make(Schedule::class)
                ->command(UnblockCommenterCommand::class)
                ->hourly();
        });
    }
}
