<?php

declare(strict_types=1);

namespace Appleton\Threads\Events;

use Appleton\Threads\Models\BlockedCommenter;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommenterBlocked
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(private BlockedCommenter $blockedCommenter)
    {
    }

    public function getBlockedCommenter(): BlockedCommenter
    {
        return $this->blockedCommenter;
    }
}
