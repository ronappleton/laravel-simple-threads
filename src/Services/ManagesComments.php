<?php

declare(strict_types=1);

namespace Appleton\Threads\Services;

use Appleton\Threads\Events\CommentCreated;
use Appleton\Threads\Http\Requests\CreateCommentRequest;
use Appleton\Threads\Http\Requests\UpdateCommentRequest;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Carbon\Carbon;

trait ManagesComments
{
    public function createComment(Thread $thread, CreateCommentRequest $request): void
    {
        $comment = Comment::create($request->validated() + [
            'thread_id' => $thread->id,
            'user_id' => auth()->id(),
        ]);

        event(new CommentCreated($comment));
    }

    public function updateComment(Comment $comment, UpdateCommentRequest $request): void
    {
        $comment->update($request->validated());
    }

    public function hideComment(Comment $comment): void
    {
        $comment->update(['hidden_at' => Carbon::now()]);
    }

    public function unHideComment(Comment $comment): void
    {
        $comment->update(['hidden_at' => null]);
    }

    public function deleteComment(Comment $comment): void
    {
        $comment->delete();
    }

    public function restoreComment(Comment $comment): void
    {
        $comment->restore();
    }
}
