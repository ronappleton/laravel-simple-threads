<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\ThreadUnlocked;
use Appleton\Threads\Notifications\ThreadUnlocked as ThreadUnlockedNotification;
use Illuminate\Contracts\Auth\Authenticatable;

class ThreadUnlockedListener
{
    public function handle(ThreadUnlocked $event): void
    {
        if (!config()->boolean('threads.listeners.thread_unlocked', false)) {
            return;
        }

        $thread = $event->getThread();

        /** @var array<int, array<int, string>> $threadedUserRelations */
        $threadedUserRelations = config()->array('threads.threaded_user_relations');

        $thread->deepNestedRelations($threadedUserRelations)
            ->filter(fn ($user) => $user instanceof Authenticatable)
            /** @phpstan-ignore-next-line */
            ->each(fn ($user) => $user->notify(new ThreadUnlockedNotification($event->getThread())));
    }
}
