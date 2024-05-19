<?php

declare(strict_types=1);

namespace Appleton\Threads\Console\Commands;

use Appleton\Threads\Http\Requests\UnblockCommenterRequest;
use Appleton\Threads\Models\BlockedCommenter;
use Appleton\Threads\Services\ThreadService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UnblockCommenterCommand extends Command
{
    protected $signature = 'threads:unblock:commenter';

    protected $description = 'Command description';

    public function handle(ThreadService $threadService): int
    {
        $this->info('Unblocking commenters...');

        $commenters = BlockedCommenter::where('expires_at', '<=', Carbon::now())->get();

        $commenters->each(function (BlockedCommenter $commenter) use ($threadService) {
            $request =  new UnblockCommenterRequest(['unblock_reason' => 'Scheduled unblock']);
            $threadService->unblockCommenter($request, $commenter->blocked_user_id);
        });

        return self::SUCCESS;
    }
}
