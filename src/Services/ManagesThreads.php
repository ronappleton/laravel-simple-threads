<?php

declare(strict_types=1);

namespace Appleton\Threads\Services;

use Appleton\Threads\Events\LikeReceived;
use Appleton\Threads\Events\ThreadCreated;
use Appleton\Threads\Events\ThreadLocked;
use Appleton\Threads\Events\ThreadUnlocked;
use Appleton\Threads\Http\Requests\CreateThreadRequest;
use Appleton\Threads\Http\Requests\UpdateThreadRequest;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadLike;
use Carbon\Carbon;

trait ManagesThreads
{
    public function createThread(CreateThreadRequest $request): void
    {
        $thread = Thread::create($request->validated());

        event(new ThreadCreated($thread));
    }

    public function updateThread(Thread $thread, UpdateThreadRequest $request): void
    {
        $thread->update($request->validated());
    }

    public function lockThread(Thread $thread): void
    {
        $thread->update(['locked_at' => Carbon::now()]);

        event(new ThreadLocked($thread));
    }

    public function unlockThread(Thread $thread): void
    {
        $thread->update(['locked_at' => null]);

        event(new ThreadUnlocked($thread));
    }

    public function pinThread(Thread $thread): void
    {
        $thread->update(['pinned_at' => Carbon::now()]);
    }

    public function unpinThread(Thread $thread): void
    {
        $thread->update(['pinned_at' => null]);
    }

    public function hideThread(Thread $thread): void
    {
        $thread->update(['hidden_at' => Carbon::now()]);
    }

    public function unHideThread(Thread $thread): void
    {
        $thread->update(['hidden_at' => null]);
    }

    public function deleteThread(Thread $thread): void
    {
        $thread->comments()->delete();
        $thread->likes()->delete();
        $thread->delete();
    }

    public function restoreThread(string $id): void
    {
        $thread = Thread::withTrashed()->findOrFail($id);
        $thread->restore();
        $thread->comments()->restore();
        $thread->likes()->restore();
    }

    public function likeThread(Thread $thread): void
    {
        $user = auth()->user();

        $threadLike = ThreadLike::create([
            'thread_id' => $thread->id,
            /** @phpstan-ignore-next-line  */
            'user_id' => $user->id,
        ]);

        event(new LikeReceived($threadLike));
    }

    public function unlikeThread(Thread $thread): void
    {
        $user = auth()->user();

        ThreadLike::query()
            ->where('thread_id', $thread->id)
            /** @phpstan-ignore-next-line */
            ->where('user_id', $user->id)
            ->forceDelete();
    }
}
