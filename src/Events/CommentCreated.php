<?php

declare(strict_types=1);

namespace Appleton\Threads\Events;

use Appleton\Threads\Models\Comment;

readonly class CommentCreated
{
    public function __construct(private Comment $comment)
    {
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}