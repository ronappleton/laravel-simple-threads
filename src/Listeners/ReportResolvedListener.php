<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\ReportResolved;
use Appleton\Threads\Notifications\ReportResolved as ReportResolvedNotification;

class ReportResolvedListener
{
    public function handle(ReportResolved $event): void
    {
        if (!config()->boolean('threads.listeners.report_resolved', false)) {
            return;
        }

        $reportedUser = $event->getReport()->user;

        /** @phpstan-ignore-next-line */
        $reportedUser->notify(new ReportResolvedNotification($event->getReport()));
    }
}
