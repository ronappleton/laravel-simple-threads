<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Models\BlockedCommenter;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Notifications\CommentCreated;
use Appleton\Threads\Notifications\CommenterBlocked;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Mockery;
use Tests\TestCase;

class CommenterBlockedTest extends TestCase
{
    private CommenterBlocked $notification;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('threads.user_model', UserUuid::class);
        Config::set('threads.thread_show_url', 'http://localhost/thread');
        Config::set('threads.user_name_field', 'display_name');
        Config::set('threads.threaded_user_relations', [
            [
                'user',
            ],
        ]);
        Config::set('threads.notifications.commenter_blocked.sms.message', 'You have been blocked by :blocker_name');
        Config::set('threads.notifications.commenter_blocked.database.message', 'You have been blocked by :blocker_name');
        Config::set('threads.notifications.commenter_blocked.email.message', 'You have been blocked by :blocker_name');
        Config::set('threads.notifications.commenter_blocked.push.message', 'You have been blocked by :blocker_name');

        $user = new UserUuid();
        $user->setAttribute('display_name', 'Test Blocked');
        $user->setAttribute('avatar', 'some_url');

        $blockerUser = new UserUuid();
        $blockerUser->setAttribute('display_name', 'Test Blocker');
        $blockerUser->setAttribute('avatar', 'some_url');

        $blockedCommenter = new BlockedCommenter();
        $blockedCommenter->setAttribute('blockerUser', $blockerUser);

        $this->notification = new CommenterBlocked($blockedCommenter);
    }

    public function testVonageMessageIsCorrect(): void
    {
        $message = $this->notification->toVonage(new \stdClass());

        $this->assertInstanceOf(VonageMessage::class, $message);
        $this->assertSame('You have been blocked by Test Blocker', $message->content);
    }

    public function testDatabaseMessageIsCorrect(): void
    {
        $message = $this->notification->toDatabase(new \stdClass());

        $this->assertIsArray($message);
        $this->assertSame('You have been blocked by Test Blocker', $message['message']);
        $this->assertSame('Test Blocker', $message['user_name']);
        $this->assertSame('some_url', $message['avatar']);
    }

    public function testBroadcastMessageIsCorrect(): void
    {
        $message = $this->notification->toBroadcast(new \stdClass());

        $this->assertInstanceOf(BroadcastMessage::class, $message);
        $this->assertIsArray($message->data);
        
        $data = $message->data;

        $this->assertSame('You have been blocked by Test Blocker', $data['message']);
        $this->assertSame('Test Blocker', $data['user_name']);
        $this->assertSame('some_url', $data['avatar']);
    }

    public function testMailMessageIsCorrect(): void
    {
        $message = $this->notification->toMail(new \stdClass());

        $this->assertInstanceOf(MailMessage::class, $message);

        $data = $message->toArray();

        $this->assertSame('info', $data['level']);
        $this->assertSame('System Notification', $data['subject'], );
        $this->assertSame('You have been blocked by Test Blocker', $data['introLines'][0]);
    }

    public function testArrayMessageIsCorrect(): void
    {
        $message = $this->notification->toArray(new \stdClass());

        $this->assertIsArray($message);
        $this->assertSame('You have been blocked by Test Blocker', $message['message']);
        $this->assertSame('Test Blocker', $message['user_name']);
        $this->assertSame('some_url', $message['avatar']);
    }
}