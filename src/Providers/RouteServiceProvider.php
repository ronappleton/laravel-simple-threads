<?php

declare(strict_types=1);

namespace Appleton\Threads\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseRouteServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends BaseRouteServiceProvider
{
    /**
     * @var string
     */
    protected $namespace = 'Appleton\Threads\Http\Controllers';

    public function boot(): void
    {
        parent::boot();

        $this->routes(function () {
            Route::prefix(config()->string('threads.route_prefix', 'api'))
                ->namespace($this->namespace)
                ->group(__DIR__.'/../../routes/api.php');
        });
    }
}
