<?php

declare(strict_types=1);

namespace Appleton\Threads\Services;

use Appleton\Threads\Events\ReportReceived;
use Appleton\Threads\Events\ReportResolved;
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
        $threadReport = ThreadReport::create([
                'thread_id' => $thread->id,
                'user_id' => auth()->id(),
                'reason' => $request->string('reason'),
            ]);

        event(new ReportReceived($threadReport));
    }

    public function reportComment(CreateThreadReportRequest $request, Comment $comment): void
    {
        $threadReport = ThreadReport::create([
                'comment_id' => $comment->id,
                'user_id' => auth()->id(),
                'reason' => $request->string('reason'),
            ]);

        event(new ReportReceived($threadReport));
    }

    public function resolveThreadReport(ThreadReport $threadReport): void
    {
        $threadReport->update(['resolved_at' => Carbon::now()]);
        $threadReport->delete();

        event(new ReportResolved($threadReport));
    }
}
