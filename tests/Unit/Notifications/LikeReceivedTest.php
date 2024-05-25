<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadLike;
use Appleton\Threads\Notifications\CommentCreated;
use Appleton\Threads\Notifications\LikeReceived;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Mockery;
use Tests\TestCase;

class LikeReceivedTest extends TestCase
{
    private LikeReceived $notification;

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
        Config::set('threads.notifications.like_received.sms.message', 'You have a new like from :liker_name');
        Config::set('threads.notifications.like_received.database.message', 'You have a new like from :liker_name');
        Config::set('threads.notifications.like_received.email.message', 'You have a new like from :liker_name');
        Config::set('threads.notifications.like_received.push.message', 'You have a new like from :liker_name');

        $user = new UserUuid();
        $user->setAttribute('display_name', 'Test User');
        $user->setAttribute('avatar', 'some_url');

        $thread = Mockery::mock(Thread::class);
        $thread->shouldReceive('getAttribute')->with('id')->andReturn('some_string');

        $like = new ThreadLike();
        $like->setAttribute('thread', $thread);
        $like->setAttribute('user', $user);


        $this->notification = new LikeReceived($like);
    }

    public function testVonageMessageIsCorrect(): void
    {
        $message = $this->notification->toVonage(new \stdClass());

        $this->assertInstanceOf(VonageMessage::class, $message);
        $this->assertSame('You have a new like from Test User - http://localhost/thread?thread=some_string', $message->content);
    }

    public function testDatabaseMessageIsCorrect(): void
    {
        $message = $this->notification->toDatabase(new \stdClass());

        $this->assertIsArray($message);
        $this->assertSame('You have a new like from Test User', $message['message']);
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

        $this->assertSame('You have a new like from Test User', $data['message']);
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
        $this->assertSame('New Like', $data['subject']);
        $this->assertSame('You have a new like from Test User', $data['introLines'][0]);
        $this->assertSame('View Thread', $data['actionText']);
        $this->assertSame('http://localhost/thread?thread=some_string', $data['actionUrl']);
        $this->assertSame('http://localhost/thread?thread=some_string', $data['displayableActionUrl']);
    }

    public function testArrayMessageIsCorrect(): void
    {
        $message = $this->notification->toArray(new \stdClass());

        $this->assertIsArray($message);
        $this->assertSame('You have a new like from Test User', $message['message']);
        $this->assertSame('http://localhost/thread?thread=some_string', $message['url']);
        $this->assertSame('Test User', $message['user_name']);
        $this->assertSame('some_url', $message['avatar']);
    }
}