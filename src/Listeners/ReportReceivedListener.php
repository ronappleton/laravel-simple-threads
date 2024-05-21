<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\ReportReceived;
use Appleton\Threads\Notifications\ReportReceived as ReportReceivedNotification;

class ReportReceivedListener
{
    public function handle(ReportReceived $event): void
    {
        $reportedUser = $event->getReport()->comment
            ? $event->getReport()->comment->user
            : $event->getReport()->thread->user;

        /** @phpstan-ignore-next-line */
        $reportedUser->notify(new ReportReceivedNotification($event->getReport()));
    }
}
