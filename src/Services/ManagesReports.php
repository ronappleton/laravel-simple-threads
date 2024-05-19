<?php

declare(strict_types=1);

namespace Appleton\Threads\Services;

use Appleton\Threads\Http\Requests\CreateThreadReportRequest;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait ManagesReports
{
    public function reportThread(CreateThreadReportRequest $request, Thread $thread): void
    {
        DB::transaction(function () use ($request, $thread) {
            return ThreadReport::create([
                'thread_id' => $thread->id,
                'user_id' => auth()->id(),
                'reason' => $request->string('reason'),
            ]);
        });
    }

    public function reportComment(CreateThreadReportRequest $request, Comment $comment): void
    {
        DB::transaction(function () use ($request, $comment) {
            return ThreadReport::create([
                'comment_id' => $comment->id,
                'user_id' => auth()->id(),
                'reason' => $request->string('reason'),
            ]);
        });
    }

    public function resolveThreadReport(ThreadReport $threadReport): void
    {
        $threadReport->update(['resolved_at' => Carbon::now()]);
        $threadReport->delete();
    }
}
