<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\ThreadCreated;

class ThreadCreatedListener
{
    public function __construct(private readonly ThreadCreated $event)
    {
    }

    public function handle(): void
    {
        // Handle the event
    }
}