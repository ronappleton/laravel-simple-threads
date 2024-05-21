<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\LikeReceived;
use Appleton\Threads\Notifications\LikeReceived as LikeReceivedNotification;
use Illuminate\Contracts\Auth\Authenticatable;

class LikeReceivedListener
{
    public function handle(LikeReceived $event): void
    {
        if (!config()->boolean('threads.listeners.like_received', false)) {
            return;
        }

        $thread = $event->getLike()->thread;

        /** @var array<int, array<int, string>> $threadedUserRelations */
        $threadedUserRelations = config()->array('threads.threaded_user_relations');

        $thread->deepNestedRelations($threadedUserRelations)
            ->filter(fn ($user) => $user instanceof Authenticatable)
            /** @phpstan-ignore-next-line */
            ->each(fn ($user) => $user->notify(new LikeReceivedNotification($event->getLike())));
    }
}
