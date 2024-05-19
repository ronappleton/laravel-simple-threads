<?php

declare(strict_types=1);

namespace Appleton\Threads\Events;

use Appleton\Threads\Models\ThreadReport;

readonly class ReportReceived
{
    public function __construct(private ThreadReport $report)
    {
    }

    public function getReport(): ThreadReport
    {
        return $this->report;
    }
}