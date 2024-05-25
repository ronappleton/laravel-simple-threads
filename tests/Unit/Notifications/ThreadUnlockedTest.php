<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Notifications\ThreadUnlocked;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Mockery;
use Tests\TestCase;

class ThreadUnlockedTest extends TestCase
{
    private ThreadUnlocked $notification;

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
        Config::set('threads.notifications.comment_created.sms.message', 'Your thread has been unlocked');
        Config::set('threads.notifications.comment_created.database.message', 'Your thread has been unlocked');
        Config::set('threads.notifications.comment_created.email.message', 'Your thread has been unlocked');
        Config::set('threads.notifications.comment_created.push.message', 'Your thread has been unlocked');

        $user = new UserUuid();
        $user->setAttribute('display_name', 'Test User');
        $user->setAttribute('avatar', 'some_url');

        $thread = Mockery::mock(Thread::class);
        $thread->shouldReceive('getAttribute')->with('id')->andReturn('some_string');
        $thread->shouldReceive('getAttribute')->with('user')->andReturn($user);

        $this->notification = new ThreadUnlocked($thread);
    }

    public function testVonageMessageIsCorrect(): void
    {
        $message = $this->notification->toVonage(new \stdClass());

        $this->assertInstanceOf(VonageMessage::class, $message);
        $this->assertSame('Your thread has been unlocked - http://localhost/thread?thread=some_string', $message->content);
    }

    public function testDatabaseMessageIsCorrect(): void
    {
        $message = $this->notification->toDatabase(new \stdClass());

        $this->assertIsArray($message);
        $this->assertSame('Your thread has been unlocked', $message['message']);
        $this->assertSame('http://localhost/thread?thread=some_string', $message['url']);
    }

    public function testBroadcastMessageIsCorrect(): void
    {
        $message = $this->notification->toBroadcast(new \stdClass());

        $this->assertInstanceOf(BroadcastMessage::class, $message);
        $this->assertIsArray($message->data);
        
        $data = $message->data;

        $this->assertSame('Your thread has been unlocked', $data['message']);
        $this->assertSame('http://localhost/thread?thread=some_string', $data['url']);
    }

    public function testMailMessageIsCorrect(): void
    {
        $message = $this->notification->toMail(new \stdClass());

        $this->assertInstanceOf(MailMessage::class, $message);

        $data = $message->toArray();

        $this->assertSame('info', $data['level']);
        $this->assertSame('Thread Unlocked', $data['subject']);
        $this->assertSame('Your thread has been unlocked', $data['introLines'][0]);
        $this->assertSame('View Thread', $data['actionText']);
        $this->assertSame('http://localhost/thread?thread=some_string', $data['actionUrl']);
        $this->assertSame('http://localhost/thread?thread=some_string', $data['displayableActionUrl']);
    }

    public function testArrayMessageIsCorrect(): void
    {
        $message = $this->notification->toArray(new \stdClass());

        $this->assertIsArray($message);
        $this->assertSame('Your thread has been unlocked', $message['message']);
        $this->assertSame('http://localhost/thread?thread=some_string', $message['url']);
    }
}