<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\CommenterUnblocked;
use Appleton\Threads\Notifications\CommenterUnblocked as CommenterUnblockedNotification;

readonly class CommenterUnblockedListener
{
    public function __construct(private CommenterUnblocked $event)
    {
    }

    public function handle(): void
    {
        $blockedUser = $this->event->getBlockedCommenter()->blockedUser;

        /** @phpstan-ignore-next-line */
        $blockedUser->notify(new CommenterUnblockedNotification($this->event->getBlockedCommenter()));
    }
}
