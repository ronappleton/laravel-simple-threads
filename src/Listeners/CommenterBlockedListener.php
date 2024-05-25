<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\CommenterBlocked;
use Appleton\Threads\Notifications\CommenterBlocked as CommenterBlockedNotification;

class CommenterBlockedListener
{
    public function handle(CommenterBlocked $event): void
    {
        if (! config()->boolean('threads.listeners.commenter_blocked', false)) {
            return;
        }

        $userToNotify = $event->getBlockedCommenter()->blockedUser;

        /** @phpstan-ignore-next-line */
        $userToNotify->notify(new CommenterBlockedNotification($event->getBlockedCommenter()));
    }
}
