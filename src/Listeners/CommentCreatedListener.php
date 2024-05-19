<?php

declare(strict_types=1);

namespace Appleton\Threads\Listeners;

use Appleton\Threads\Events\CommentCreated;

class CommentCreatedListener
{
    public function __construct(private readonly CommentCreated $event)
    {
    }

    public function handle()
    {
        // Handle the event
    }
}
