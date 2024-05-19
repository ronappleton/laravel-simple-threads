<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

class ReportReceivedListener
{
    public function __construct(private readonly ReportReceived $event)
    {
    }

    public function handle(): void
    {
        // Handle the event
    }
}