<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\ThreadLocked;
use Appleton\Threads\Notifications\ThreadLocked as ThreadLockedNotification;
use Illuminate\Contracts\Auth\Authenticatable;

class ThreadLockedListener
{
    public function handle(ThreadLocked $event): void
    {
        if (! config()->boolean('threads.listeners.thread_locked', false)) {
            return;
        }

        $thread = $event->getThread();

        /** @var array<int, array<int, string>> $threadedUserRelations */
        $threadedUserRelations = config()->array('threads.threaded_user_relations');

        $thread->deepNestedRelations($threadedUserRelations)
            ->filter(fn ($user) => $user instanceof Authenticatable)
            /** @phpstan-ignore-next-line */
            ->each(fn ($user) => $user->notify(new ThreadLockedNotification($event->getThread())));
    }
}
