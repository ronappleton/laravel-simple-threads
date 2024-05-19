<?php

declare(strict_types=1);

namespace Appleton\Threads\Events;

use Appleton\Threads\Models\BlockedCommenter;

readonly class CommenterUnblocked
{
    public function __construct(private BlockedCommenter $blockedCommenter)
    {
    }

    public function getBlockedCommenter(): BlockedCommenter
    {
        return $this->blockedCommenter;
    }
}