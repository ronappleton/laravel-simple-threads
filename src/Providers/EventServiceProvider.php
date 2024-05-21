<?php

declare(strict_types=1);

namespace Appleton\Threads\Providers;

use Appleton\Threads\Events\CommentCreated;
use Appleton\Threads\Events\CommenterBlocked;
use Appleton\Threads\Events\CommenterUnblocked;
use Appleton\Threads\Events\LikeReceived;
use Appleton\Threads\Events\ReportReceived;
use Appleton\Threads\Events\ReportResolved;
use Appleton\Threads\Events\ThreadCreated;
use Appleton\Threads\Events\ThreadLocked;
use Appleton\Threads\Events\ThreadUnlocked;
use Appleton\Threads\Listeners\CommentCreatedListener;
use Appleton\Threads\Listeners\CommenterBlockedListener;
use Appleton\Threads\Listeners\CommenterUnblockedListener;
use Appleton\Threads\Listeners\LikeReceivedListener;
use Appleton\Threads\Listeners\ReportReceivedListener;
use Appleton\Threads\Listeners\ReportResolvedListener;
use Appleton\Threads\Listeners\ThreadCreatedListener;
use Appleton\Threads\Listeners\ThreadLockedListener;
use Appleton\Threads\Listeners\ThreadUnlockedListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        CommentCreated::class => [
            CommentCreatedListener::class,
        ],
        CommenterBlocked::class => [
            CommenterBlockedListener::class,
        ],
        CommenterUnblocked::class => [
            CommenterUnblockedListener::class,
        ],

        LikeReceived::class => [
            LikeReceivedListener::class,
        ],
        ReportReceived::class => [
            ReportReceivedListener::class,
        ],
        ReportResolved::class => [
            ReportResolvedListener::class,
        ],
        ThreadCreated::class => [
            ThreadCreatedListener::class,
        ],
        ThreadLocked::class => [
            ThreadLockedListener::class,
        ],
        ThreadUnlocked::class => [
            ThreadUnlockedListener::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
