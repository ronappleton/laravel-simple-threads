<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Models\BlockedCommenter;
use Carbon\Carbon;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class UnblockCommentersTest extends TestCase
{
    public function testCanUnBlockCommenters(): void
    {
        $this->artisan('threads:unblock:commenter')
            ->expectsOutput('Unblocking commenters...')
            ->assertExitCode(0);
    }

    public function testUnblocksCommenters(): void
    {
        config()->set('threads.user_model', UserUuid::class);

        TestTime::freeze(Carbon::now());

        $blocker = $this->getNewUser();
        $blocked = $this->getNewUser();
        $blocked2 = $this->getNewUser();
        $blocked3 = $this->getNewUser();
        $blocked4 = $this->getNewUser();

        foreach ([$blocked, $blocked2, $blocked3] as $blockedUser) {
            BlockedCommenter::factory()->create([
                'blocker_user_id' => $blocker->id,
                'blocked_user_id' => $blockedUser->id,
                'expires_at' => Carbon::now()->subDay(),
            ]);
        }

        BlockedCommenter::factory()->create([
            'blocker_user_id' => $blocker->id,
            'blocked_user_id' => $blocked4->id,
            'expires_at' => Carbon::now()->addDay(),
        ]);

        foreach ([$blocked, $blocked2, $blocked3] as $blockedUser) {
            $this->assertDatabaseHas('blocked_commenters', [
                'blocker_user_id' => $blocker->id,
                'blocked_user_id' => $blockedUser->id,
                'deleted_at' => null,
            ]);
        }

        $this->assertDatabaseHas('blocked_commenters', [
            'blocker_user_id' => $blocker->id,
            'blocked_user_id' => $blocked4->id,
            'deleted_at' => null,
        ]);

        $this->artisan('threads:unblock:commenter')
            ->expectsOutput('Unblocking commenters...')
            ->assertExitCode(0);

        foreach ([$blocked, $blocked2, $blocked3] as $blockedUser) {
            $this->assertDatabaseHas('blocked_commenters', [
                'blocked_user_id' => $blockedUser->id,
                'deleted_at' => Carbon::now()->toDateTimeString(),
            ]);
        }

        $this->assertDatabaseHas('blocked_commenters', [
            'blocked_user_id' => $blocked4->id,
            'deleted_at' => null,
        ]);
    }
}
