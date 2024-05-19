<?php

declare(strict_types=1);

namespace Appleton\Threads;

use Appleton\Threads\Console\Commands\UnblockCommenterCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/threads.php', 'threads');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/threads.php' => config_path('threads.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if (app()->environment('testing')) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations/testing');
        }

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
