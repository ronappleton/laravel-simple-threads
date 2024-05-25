<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Events\CommenterUnblocked;
use Appleton\Threads\Models\BlockedCommenter;
use Appleton\Threads\Notifications\CommenterUnblocked as CommenterUnblockedNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CommenterUnblockedListenerTest extends TestCase
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
        Config::set('threads.listeners.commenter_unblocked', true);
        Config::set('threads.notifications.commenter_unblocked.sms.enabled', true);
        Config::set('threads.notifications.commenter_unblocked.database.enabled', true);
        Config::set('threads.notifications.commenter_unblocked.email.enabled', true);
        Config::set('threads.notifications.commenter_unblocked.push.enabled', true);
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

        event(new CommenterUnblocked($blockedCommenter));

        Notification::assertSentTo($blockedCommenter->blockedUser, CommenterUnblockedNotification::class,);
    }

    public function testHandleDoesNotSendNotificationWhenConfigIsFalse(): void
    {
        Config::set('threads.listeners.commenter_unblocked', false);

        $user = $this->getNewUser();
        $blocker = $this->getNewUser();

        $blockedCommenter = BlockedCommenter::factory()->create([
            'blocked_user_id' => $user->id,
            'blocker_user_id' => $blocker->id,
        ]);

        Notification::fake();

        event(new CommenterUnblocked($blockedCommenter));

        Notification::assertNothingSent();
    }
}