<?php

declare(strict_types=1);

namespace Appleton\Threads\Policies;

use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class CommentPolicy
{
    use HandlesAuthorization;

    public function show(?Authenticatable $user, Comment $comment): bool
    {
        if ($user && $this->can($user, 'threads.comments.show')) {
            return true;
        }

        if ($comment->thread->reports()->exists()) {
            return false;
        }

        return true;
    }

    public function create(Authenticatable $user, Thread $thread): bool
    {
        if ($thread->reports()->exists()) {
            return false;
        }

        if ($thread->locked_at) {
            return false;
        }

        return true;
    }

    public function update(Authenticatable $user, Comment $comment): bool
    {
        if ($comment->thread->reports()->exists()) {
            return false;
        }

        if ($comment->reports()->exists()) {
            return false;
        }

        if ($comment->thread->locked_at) {
            return false;
        }

        return $this->userIsOwner($user, $comment);
    }

    public function delete(Authenticatable $user, Comment $comment): bool
    {
        if ($this->can($user, 'threads.comments.delete')) {
            return true;
        }

        if ($comment->thread->reports()->exists()) {
            return false;
        }

        if ($comment->reports()->exists()) {
            return false;
        }

        return $this->userIsOwner($user, $comment);
    }

    public function restore(Authenticatable $user, Comment $comment): bool
    {
        if ($comment->thread->reports()->exists()) {
            return false;
        }

        if ($comment->reports()->exists()) {
            return false;
        }

        if ($comment->thread->locked_at) {
            return false;
        }

        return $this->userIsOwner($user, $comment);
    }

    public function hide(Authenticatable $user, Comment $comment): bool
    {
        if ($this->can($user, 'threads.comments.hide')) {
            return true;
        }

        if ($comment->thread->reports()->exists()) {
            return false;
        }

        if ($comment->reports()->exists()) {
            return false;
        }

        return $this->userIsOwner($user, $comment);
    }

    public function block(Authenticatable $user): bool
    {
        return $this->can($user, 'threads.commenter.block');
    }

    public function unblock(Authenticatable $user, string $unblock): bool
    {
        if ($user->id === $unblock) {
            return false;
        }

        return $this->can($user, 'threads.commenter.unblock');
    }

    private function userIsOwner(Authenticatable $user, Comment $comment): bool
    {
        return method_exists($user, 'getAttribute')
            &&  $user->getAttribute('id') === $comment->user_id;
    }

    private function can(Authenticatable $user, string $ability): bool
    {
        return method_exists($user, 'can') && $user->can($ability);
    }
}
