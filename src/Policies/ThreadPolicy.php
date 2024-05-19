<?php

declare(strict_types=1);

namespace Appleton\Threads\Policies;

use Appleton\Threads\Models\Thread;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class ThreadPolicy
{
    use HandlesAuthorization;

    public function show(?Authenticatable $user, Thread $thread): bool
    {
        if ($user && $this->can($user, 'threads.comments.show')) {
            return true;
        }

        if ($thread->reports()->exists()) {
            return false;
        }

        return true;
    }

    public function create(Authenticatable $user): bool
    {
        // User only needs to be authenticated to create a thread
        return true;
    }

    public function update(Authenticatable $user, Thread $thread): bool
    {
        if ($thread->reports()->exists()) {
            return false;
        }

        return $this->userIsOwner($user, $thread);
    }

    public function delete(Authenticatable $user, Thread $thread): bool
    {
        if ($this->can($user, 'threads.delete')) {
            return true;
        }

        if ($thread->reports()->exists()) {
            return false;
        }

        return $this->userIsOwner($user, $thread);
    }

    public function restore(Authenticatable $user, Thread $thread): bool
    {
        if ($thread->reports()->exists()) {
            return false;
        }

        return $this->userIsOwner($user, $thread);
    }

    public function lock(Authenticatable $user, Thread $thread): bool
    {
        if ($this->can($user, 'threads.lock')) {
            return true;
        }

        if ($thread->reports()->exists() && $thread->locked_at !== null) {
            return false;
        }

        return $this->userIsOwner($user, $thread);
    }

    public function pin(Authenticatable $user, Thread $thread): bool
    {
        if ($thread->reports()->exists()) {
            return false;
        }

        return $this->userIsOwner($user, $thread);
    }

    public function hide(Authenticatable $user, Thread $thread): bool
    {
        if ($this->can($user, 'threads.hide')) {
            return true;
        }

        // Prevent un hiding if the thread has reports and the thread is hidden
        if ($thread->reports()->exists() && $thread->hidden_at !== null) {
            return false;
        }

        return $this->userIsOwner($user, $thread);
    }

    private function userIsOwner(Authenticatable $user, Thread $thread): bool
    {
        return method_exists($user, 'getAttribute')
            &&  $user->getAttribute('id') === $thread->user_id;
    }

    private function can(Authenticatable $user, string $ability): bool
    {
        return method_exists($user, 'can') && $user->can($ability);
    }
}
