<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Models\ThreadReport;

class ReportResolvedListener
{
    public function __construct(private readonly ThreadReport $report)
    {
    }

    public function handle(): void
    {
        // Handle the event
    }
}
