<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\CommenterUnblocked;
use Appleton\Threads\Notifications\CommenterUnblocked as CommenterUnblockedNotification;

class CommenterUnblockedListener
{
    public function handle(CommenterUnblocked $event): void
    {
        if (! config()->boolean('threads.listeners.commenter_unblocked', false)) {
            return;
        }

        $blockedUser = $event->getBlockedCommenter()->blockedUser;

        /** @phpstan-ignore-next-line */
        $blockedUser->notify(new CommenterUnblockedNotification($event->getBlockedCommenter()));
    }
}
