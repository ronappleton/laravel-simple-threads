<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\ReportReceived;
use Appleton\Threads\Notifications\ReportReceived as ReportReceivedNotification;

readonly class ReportReceivedListener
{
    public function __construct(private ReportReceived $event)
    {
    }

    public function handle(): void
    {
        $reportedUser = $this->event->getReport()->user;

        /** @phpstan-ignore-next-line */
        $reportedUser->notify(new ReportReceivedNotification($this->event->getReport()));
    }
}
