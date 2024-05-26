<?php

declare(strict_types=1);

namespace Appleton\Threads\Services;

use Appleton\Threads\Events\CommenterBlocked;
use Appleton\Threads\Events\CommenterUnblocked;
use Appleton\Threads\Http\Requests\CreateBlockCommenterRequest;
use Appleton\Threads\Http\Requests\UnblockCommenterRequest;
use Appleton\Threads\Models\BlockedCommenter;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Carbon\Carbon;

trait ManagesCommenters
{
    public function blockCommenter(CreateBlockCommenterRequest $request, string $user): void
    {
        $user = threads_get_user($user);

        $blockedCommenter = BlockedCommenter::create($request->validated() + [
            'blocker_user_id' => auth()->id(),
            /** @phpstan-ignore-next-line */
            'blocked_user_id' => $user->id,
        ]);

        /** @phpstan-ignore-next-line */
        Thread::where('user_id', $user->id)
            ->update(['hidden_at' => Carbon::now()]);

        /** @phpstan-ignore-next-line */
        Comment::where('user_id', $user->id)
            ->update(['hidden_at' => Carbon::now()]);

        event(new CommenterBlocked($blockedCommenter));
    }

    public function unblockCommenter(UnblockCommenterRequest $request, string $user): void
    {
        $user = threads_get_user($user);

        /** @phpstan-ignore-next-line */
        $blockedCommenter = BlockedCommenter::where('blocked_user_id', $user->id)
            ->firstOrFail();

        $blockedCommenter->update(['unblock_reason' => $request->unblock_reason]);

        $blockedCommenter->delete();

        /** @phpstan-ignore-next-line */
        Thread::where('user_id', $user->id)
            ->update(['hidden_at' => null]);

        /** @phpstan-ignore-next-line */
        Comment::where('user_id', $user->id)
            ->update(['hidden_at' => null]);

        event(new CommenterUnblocked($blockedCommenter));
    }
}
