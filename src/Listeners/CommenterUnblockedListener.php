<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\CommenterUnblocked;

class CommenterUnblockedListener
{
    public function __construct(private readonly CommenterUnblocked $event)
    {
    }

    public function handle(): void
    {
        // Handle the event
    }
}
