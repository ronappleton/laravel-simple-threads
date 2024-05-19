<?php

declare(strict_types=1);

namespace Appleton\Threads\Providers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class EventServiceProvider extends ServiceProvider
{
    private string $eventFqdn = 'Appleton\Threads\Events\\';

    private string $listenerFqdn = 'Appleton\Threads\Listeners\\';

    /**
     * The events and their listeners that should be registered.
     *
     * @var array<int, string>
     */
    protected array $shouldListen = [
        'comment_created',
        'commenter_blocked',
        'commenter_unblocked',
        'like_received',
        'report_received',
        'report_resolved',
        'thread_created',
        'thread_locked',
        'thread_unlocked',
    ];

    public function boot(Dispatcher $events): void
    {
        collect($this->shouldListen)
            ->filter(fn (string $event) => $this->shouldListen($event))
            ->each(fn (string $event) => $this->listen($event));
    }

    private function shouldListen(string $event): bool
    {
        return config()->boolean("threads.listeners.{$event}", false);
    }

    private function listen(string $event): void
    {
        Event::listen($this->getEvent($event), $this->getListener($event));
    }

    private function getEvent(string $event): string
    {
        return sprintf('%s%s', $this->eventFqdn, Str::studly($event));
    }

    /**
     * @return array<int, string>
     */
    private function getListener(string $event): array
    {
        return [sprintf('%s%sListener', $this->listenerFqdn, Str::studly($event)), 'handle'];
    }
}
