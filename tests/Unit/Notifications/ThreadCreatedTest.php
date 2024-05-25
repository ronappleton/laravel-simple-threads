<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Notifications\ThreadCreated;
use Appleton\Threads\Models\Thread;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Mockery;
use Tests\TestCase;

class ThreadCreatedTest extends TestCase
{
    private ThreadCreated $notification;

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
        Config::set('threads.notifications.comment_created.sms.message', 'You have a new thread from :creator_name');
        Config::set('threads.notifications.comment_created.database.message', 'You have a new thread from :creator_name');
        Config::set('threads.notifications.comment_created.email.message', 'You have a new thread from :creator_name');
        Config::set('threads.notifications.comment_created.push.message', 'You have a new thread from :creator_name');

        $user = new UserUuid();
        $user->setAttribute('display_name', 'Test User');
        $user->setAttribute('avatar', 'some_url');

        $thread = Mockery::mock(Thread::class);
        $thread->shouldReceive('getAttribute')->with('id')->andReturn('some_string');
        $thread->shouldReceive('getAttribute')->with('user')->andReturn($user);

        $this->notification = new ThreadCreated($thread);
    }

    public function testVonageMessageIsCorrect(): void
    {
        $message = $this->notification->toVonage(new \stdClass());

        $this->assertInstanceOf(VonageMessage::class, $message);
        $this->assertSame('You have a new thread from Test User - http://localhost/thread?thread=some_string', $message->content);
    }

    public function testDatabaseMessageIsCorrect(): void
    {
        $message = $this->notification->toDatabase(new \stdClass());

        $this->assertIsArray($message);
        $this->assertSame('You have a new thread from Test User', $message['message']);
        $this->assertSame('http://localhost/thread?thread=some_string', $message['url']);
        $this->assertSame('Test User', $message['user_name']);
        $this->assertSame('some_url', $message['avatar']);
    }

    public function testBroadcastMessageIsCorrect(): void
    {
        $message = $this->notification->toBroadcast(new \stdClass());

        $this->assertInstanceOf(BroadcastMessage::class, $message);
        $this->assertIsArray($message->data);
        
        $data = $message->data;

        $this->assertSame('You have a new thread from Test User', $data['message']);
        $this->assertSame('http://localhost/thread?thread=some_string', $data['url']);
        $this->assertSame('Test User', $data['user_name']);
        $this->assertSame('some_url', $data['avatar']);
    }

    public function testMailMessageIsCorrect(): void
    {
        $message = $this->notification->toMail(new \stdClass());

        $this->assertInstanceOf(MailMessage::class, $message);

        $data = $message->toArray();

        $this->assertSame('info', $data['level']);
        $this->assertSame('New Thread', $data['subject']);
        $this->assertSame('You have a new thread from Test User', $data['introLines'][0]);
        $this->assertSame('View Thread', $data['actionText']);
        $this->assertSame('http://localhost/thread?thread=some_string', $data['actionUrl']);
        $this->assertSame('http://localhost/thread?thread=some_string', $data['displayableActionUrl']);
    }

    public function testArrayMessageIsCorrect(): void
    {
        $message = $this->notification->toArray(new \stdClass());

        $this->assertIsArray($message);
        $this->assertSame('You have a new thread from Test User', $message['message']);
        $this->assertSame('http://localhost/thread?thread=some_string', $message['url']);
        $this->assertSame('Test User', $message['user_name']);
        $this->assertSame('some_url', $message['avatar']);
    }
}