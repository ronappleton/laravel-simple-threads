<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\ReportResolved;
use Appleton\Threads\Notifications\ReportResolved as ReportResolvedNotification;

readonly class ReportResolvedListener
{
    public function __construct(private ReportResolved $event)
    {
    }

    public function handle(): void
    {
        $reportedUser = $this->event->getReport()->user;

        /** @phpstan-ignore-next-line */
        $reportedUser->notify(new ReportResolvedNotification($this->event->getReport()));
    }
}
