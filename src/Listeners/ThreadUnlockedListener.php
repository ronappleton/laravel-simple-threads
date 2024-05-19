<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\ThreadUnlocked;

class ThreadUnlockedListener
{
    public function __construct(private readonly ThreadUnlocked $event)
    {
    }

    public function handle(): void
    {
        // Handle the event
    }
}