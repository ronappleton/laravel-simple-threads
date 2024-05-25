<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Notifications\CommenterUnblocked;
use Appleton\Threads\Models\BlockedCommenter;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Tests\TestCase;

class CommenterUnblockedTest extends TestCase
{
    private CommenterUnblocked $notification;

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
        Config::set('threads.notifications.commenter_unblocked.sms.message', 'You have been unblocked by :unblocker_name');
        Config::set('threads.notifications.commenter_unblocked.database.message', 'You have been unblocked by :unblocker_name');
        Config::set('threads.notifications.commenter_unblocked.email.message', 'You have been unblocked by :unblocker_name');
        Config::set('threads.notifications.commenter_unblocked.push.message', 'You have been unblocked by :unblocker_name');

        $user = new UserUuid();
        $user->setAttribute('display_name', 'Test User');
        $user->setAttribute('avatar', 'some_url');

        $blockedCommenter = new BlockedCommenter();
        $blockedCommenter->setAttribute('blockerUser', $user);

        $this->notification = new CommenterUnblocked($blockedCommenter);
    }

    public function testVonageMessageIsCorrect(): void
    {
        $message = $this->notification->toVonage(new \stdClass());

        $this->assertInstanceOf(VonageMessage::class, $message);
        $this->assertSame('You have been unblocked by Test User', $message->content);
    }

    public function testDatabaseMessageIsCorrect(): void
    {
        $message = $this->notification->toDatabase(new \stdClass());

        $this->assertIsArray($message);
        $this->assertSame('You have been unblocked by Test User', $message['message']);
        $this->assertSame('Test User', $message['user_name']);
        $this->assertSame('some_url', $message['avatar']);
    }

    public function testBroadcastMessageIsCorrect(): void
    {
        $message = $this->notification->toBroadcast(new \stdClass());

        $this->assertInstanceOf(BroadcastMessage::class, $message);
        $this->assertIsArray($message->data);
        
        $data = $message->data;

        $this->assertSame('You have been unblocked by Test User', $data['message']);
        $this->assertSame('Test User', $data['user_name']);
        $this->assertSame('some_url', $data['avatar']);
    }

    public function testMailMessageIsCorrect(): void
    {
        $message = $this->notification->toMail(new \stdClass());

        $this->assertInstanceOf(MailMessage::class, $message);

        $data = $message->toArray();

        $this->assertSame('info', $data['level']);
        $this->assertSame('System Notification', $data['subject']);
        $this->assertSame('You have been unblocked by Test User', $data['introLines'][0]);
    }

    public function testArrayMessageIsCorrect(): void
    {
        $message = $this->notification->toArray(new \stdClass());

        $this->assertIsArray($message);
        $this->assertSame('You have been unblocked by Test User', $message['message']);
        $this->assertSame('Test User', $message['user_name']);
        $this->assertSame('some_url', $message['avatar']);
    }
}