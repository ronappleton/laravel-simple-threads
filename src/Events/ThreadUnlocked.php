<?php

declare(strict_types=1);

namespace Appleton\Threads\Events;

use Appleton\Threads\Models\Thread;

readonly class ThreadUnlocked
{
    public function __construct(private Thread $thread)
    {
    }

    public function getThread(): Thread
    {
        return $this->thread;
    }
}
