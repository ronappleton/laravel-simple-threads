<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\CommenterBlocked;

class CommenterBlockedListener
{
    public function __construct(private readonly CommenterBlocked $event)
    {
    }

    public function handle(): void
    {
        // Handle the event
    }
}
