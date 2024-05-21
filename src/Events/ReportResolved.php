<?php

declare(strict_types=1);

namespace Appleton\Threads\Events;

use Appleton\Threads\Models\ThreadReport;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportResolved
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(private ThreadReport $report)
    {
    }

    public function getReport(): ThreadReport
    {
        return $this->report;
    }
}
