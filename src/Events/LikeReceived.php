<?php

declare(strict_types=1);

namespace Appleton\Threads\Events;

use Appleton\Threads\Models\ThreadLike;

readonly class LikeReceived
{
    public function __construct(private ThreadLike $like)
    {
    }

    public function getLike(): ThreadLike
    {
        return $this->like;
    }
}