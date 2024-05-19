<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\CommenterBlocked;
use Appleton\Threads\Notifications\CommenterBlocked as CommenterBlockedNotification;

readonly class CommenterBlockedListener
{
    public function __construct(private CommenterBlocked $event)
    {
    }

    public function handle(): void
    {
        $userToNotify = $this->event->getBlockedCommenter()->blockedUser;

        /** @phpstan-ignore-next-line */
        $userToNotify->notify(new CommenterBlockedNotification($this->event->getBlockedCommenter()));
    }
}
