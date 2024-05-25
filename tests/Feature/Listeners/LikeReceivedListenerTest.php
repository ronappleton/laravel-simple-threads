<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Events\LikeReceived;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadLike;
use Appleton\Threads\Notifications\LikeReceived as LikeReceivedNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LikeReceivedListenerTest extends TestCase
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
        Config::set('threads.listeners.like_received', true);
        Config::set('threads.notifications.like_received.sms.enabled', true);
        Config::set('threads.notifications.like_received.database.enabled', true);
        Config::set('threads.notifications.like_received.email.enabled', true);
        Config::set('threads.notifications.like_received.push.enabled', true);
        Config::set('threads.threaded_user_relations', [
            [
                'threaded',
                'user',
            ],
        ]);

        $user = $this->getNewUser();
        $threadedUser = $this->getNewUser();
        $threaded = $this->getNewThreaded();
        $threaded->user_id = $threadedUser->id;
        $threaded->save();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $threadLike = ThreadLike::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);

        Notification::fake();

        event(new LikeReceived($threadLike));

        Notification::assertSentTo($thread->threaded->user, LikeReceivedNotification::class,);
    }

    public function testHandleDoesNotSendNotificationWhenConfigIsFalse(): void
    {
        Config::set('threads.listeners.like_received', false);

        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $threadLike = ThreadLike::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);

        Notification::fake();

        event(new LikeReceived($threadLike));

        Notification::assertNothingSent();
    }
}