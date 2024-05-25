<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\CommentCreated;
use Appleton\Threads\Notifications\CommentCreated as CommentCreatedNotification;
use Illuminate\Contracts\Auth\Authenticatable;

class CommentCreatedListener
{
    public function handle(CommentCreated $event): void
    {
        if (! config()->boolean('threads.listeners.comment_created', false)) {
            return;
        }

        $thread = $event->getComment()->thread;

        /** @var array<int, array<int, string>> $threadedUserRelations */
        $threadedUserRelations = config()->array('threads.threaded_user_relations');

        $thread->deepNestedRelations($threadedUserRelations)
            ->filter(fn ($user) => $user instanceof Authenticatable)
            /** @phpstan-ignore-next-line */
            ->each(fn ($user) => $user->notify(new CommentCreatedNotification($event->getComment())));
    }
}
