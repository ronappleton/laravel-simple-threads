<?php

declare(strict_types=1);

namespace Appleton\Threads\Services;

use Appleton\Threads\DTOs\BlockedCommenterDTO;
use Appleton\Threads\Http\Requests\CreateBlockCommenterRequest;
use Appleton\Threads\Http\Requests\UnblockCommenterRequest;
use Appleton\Threads\Models\BlockedCommenter;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ManagesCommenters
{
    public function blockCommenter(CreateBlockCommenterRequest $request, string $user): void
    {
        $user = threads_get_user($user);

        BlockedCommenter::create($request->validated() + [
            'blocker_user_id' => auth()->id(),
            'blocked_user_id' => $user->id,
        ]);

        Thread::where('user_id', $user->id)
            ->update(['hidden_at' => Carbon::now()]);

        Comment::where('user_id', $user->id)
            ->update(['hidden_at' => Carbon::now()]);
    }

    public function unblockCommenter(UnblockCommenterRequest $request, string $user): void
    {
        $user = threads_get_user($user);

        BlockedCommenter::where('blocked_user_id', $user->id)
            ->update(['unblock_reason' => $request->unblock_reason]);

        BlockedCommenter::where('blocked_user_id', $user->id)
            ->delete();

        Thread::where('user_id', $user->id)
            ->update(['hidden_at' => null]);

        Comment::where('user_id', $user->id)
            ->update(['hidden_at' => null]);
    }
}
