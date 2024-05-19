<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Models\BlockedCommenter;
use Tests\TestCase;

class BlockedCommenterTest extends TestCase
{
    public function testCanGetBlockerUser(): void
    {
        config()->set('threads.user_model', UserUuid::class);

        $blocker = $this->getNewUser();
        $blocked = $this->getNewUser();

        $blockedCommenter = BlockedCommenter::factory()->create([
            'blocker_user_id' => $blocker->id,
            'blocked_user_id' => $blocked->id,
        ]);

        $fetchedBlocker = $blockedCommenter->blockerUser;

        $this->assertEquals($blocker->id, $fetchedBlocker->id);
    }

    public function testCanGetBlockedUser(): void
    {
        config()->set('threads.user_model', UserUuid::class);

        $blocker = $this->getNewUser();
        $blocked = $this->getNewUser();

        $blockedCommenter = BlockedCommenter::factory()->create([
            'blocker_user_id' => $blocker->id,
            'blocked_user_id' => $blocked->id,
        ]);

        $fetchedBlocked = $blockedCommenter->blockedUser;

        $this->assertEquals($blocked->id, $fetchedBlocked->id);
    }
}