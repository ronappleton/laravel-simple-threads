<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Events\CommenterBlocked;
use Appleton\Threads\Models\BlockedCommenter;
use Appleton\Threads\Notifications\CommenterBlocked as CommenterBlockedNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CommenterBlockedListenerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('threads.user_model', UserUuid::class);
        Config::set('threads.thread_show_url', 'http://localhost/thread');
    }

    public function testHandleSendsNotificationWhenConfigIsTrue(): void
    {
        Config::set('threads.listeners.commenter_blocked', true);
        Config::set('threads.notifications.commenter_blocked.sms.enabled', true);
        Config::set('threads.notifications.commenter_blocked.database.enabled', true);
        Config::set('threads.notifications.commenter_blocked.email.enabled', true);
        Config::set('threads.notifications.commenter_blocked.push.enabled', true);
        Config::set('threads.threaded_user_relations', [
            [
                'blockedUser',
            ],
        ]);

        $user = $this->getNewUser();
        $blocker = $this->getNewUser();

        $blockedCommenter = BlockedCommenter::factory()->create([
            'blocked_user_id' => $user->id,
            'blocker_user_id' => $blocker->id,
        ]);

        Notification::fake();

        event(new CommenterBlocked($blockedCommenter));

        Notification::assertSentTo($blockedCommenter->blockedUser, CommenterBlockedNotification::class,);
    }

    public function testHandleDoesNotSendNotificationWhenConfigIsFalse(): void
    {
        Config::set('threads.listeners.commenter_blocked', false);

        $user = $this->getNewUser();
        $blocker = $this->getNewUser();

        $blockedCommenter = BlockedCommenter::factory()->create([
            'blocked_user_id' => $user->id,
            'blocker_user_id' => $blocker->id,
        ]);

        Notification::fake();

        event(new CommenterBlocked($blockedCommenter));

        Notification::assertNothingSent();
    }
}