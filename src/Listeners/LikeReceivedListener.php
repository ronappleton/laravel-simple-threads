<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\LikeReceived;

class LikeReceivedListener
{
    public function __construct(private readonly LikeReceived $event)
    {
    }

    public function handle(): void
    {
        // Handle the event
    }
}