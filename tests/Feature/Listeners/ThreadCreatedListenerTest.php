<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Events\ThreadCreated;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Notifications\ThreadCreated as ThreadCreatedNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ThreadCreatedListenerTest extends TestCase
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
        Config::set('threads.listeners.thread_created', true);
        Config::set('threads.notifications.thread_created.sms.enabled', true);
        Config::set('threads.notifications.thread_created.database.enabled', true);
        Config::set('threads.notifications.thread_created.email.enabled', true);
        Config::set('threads.notifications.thread_created.push.enabled', true);
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

        Notification::fake();

        event(new ThreadCreated($thread));

        Notification::assertSentTo($thread->threaded->user, ThreadCreatedNotification::class);
    }

    public function testHandleDoesNotSendNotificationWhenConfigIsFalse(): void
    {
        Config::set('threads.listeners.thread_created', false);

        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        Notification::fake();

        event(new ThreadCreated($thread));

        Notification::assertNothingSent();
    }
}
