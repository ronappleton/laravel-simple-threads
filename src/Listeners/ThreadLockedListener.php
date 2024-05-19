<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\ThreadLocked;

class ThreadLockedListener
{
    public function __construct(private readonly ThreadLocked $event)
    {
    }

    public function handle(): void
    {
        // Handle the event
    }
}
