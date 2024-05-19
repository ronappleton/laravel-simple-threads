<?php

declare(strict_types=1);

namespace Tests;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Providers\RouteServiceProvider;
use Appleton\Threads\ServiceProvider;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('mock-permissions.uuids', true);

        $this->artisan('migrate:fresh', ['--database' => 'sqlite']);

        Schema::create('threaded', function ($table) {
            $table->uuid('id')->primary();
            $table->timestamps();
        });

        config()->set([
            'auth.guards.api.driver' => 'session',
            'auth.guards.api.provider' => 'users',
            'auth.providers.users.driver' => 'eloquent',
            'auth.providers.users.model' => UserUuid::class,
        ]);
    }

    protected function getNewThreaded(): Model
    {
        $threaded = new class extends Model
        {
            use HasUuids;

            protected $table = 'threaded';

            public function threads(): MorphMany
            {
                return $this->morphMany(Thread::class, 'threaded');
            }
        };

        $threaded->save();

        return $threaded;
    }

    protected function getNewUser(): Model
    {
        return UserUuid::factory()->create();
    }

    /**
     * @return array<int, class-string>)
     */
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
            RouteServiceProvider::class,
            \Appleton\TypedConfig\ServiceProvider::class,
            PermissionServiceProvider::class,
            \Appleton\SpatieLaravelPermissionMock\ServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
