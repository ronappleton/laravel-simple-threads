<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\ThreadCreated;
use Appleton\Threads\Notifications\ThreadCreated as ThreadCreatedNotification;
use Illuminate\Contracts\Auth\Authenticatable;

class ThreadCreatedListener
{
    public function handle(ThreadCreated $event): void
    {
        if (! config()->boolean('threads.listeners.thread_created', false)) {
            return;
        }

        $thread = $event->getThread();

        /** @var array<int, array<int, string>> $deepNestedRelations */
        $deepNestedRelations = config()->array('threads.threaded_user_relations');

        $thread->deepNestedRelations($deepNestedRelations)
            ->filter(fn ($user) => $user instanceof Authenticatable)
            /** @phpstan-ignore-next-line */
            ->each(fn ($user) => $user->notify(new ThreadCreatedNotification($event->getThread())));
    }
}
