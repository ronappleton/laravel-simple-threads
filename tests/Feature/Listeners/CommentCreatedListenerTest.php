<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Events\CommentCreated;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Notifications\CommentCreated as CommentCreatedNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CommentCreatedListenerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('threads.user_model', UserUuid::class);
        Config::set('threads.thread_show_url', 'http::/localhost/thread');
    }

    public function testHandleSendsNotificationWhenConfigIsTrue(): void
    {
        Config::set('threads.listeners.comment_created', true);

        Config::set('threads.notifications.comment_created.sms_enabled', true);

        Config::set('threads.threaded_user_relations', [
            [
                'user',
            ],
        ]);

        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'thread_id' => $thread->id,
        ]);

        Notification::fake();

        event(new CommentCreated($comment));

        Notification::assertSentTo(
            $comment->user,
            CommentCreatedNotification::class,
            function ($notification, $channels, $notifiable) use ($user) {
                return in_array('sms', $channels) &&
                    $notifiable->id === $user->id &&
                    $notification->getMessage('sms', $notification->getNameField()) === 'A new comment has been posted on a thread you are following.' &&
                    $notification->url === route('threads.show', $notification->getThreadShowUrl($notification->getComment()->thread->id));
            }
        );
    }

    public function testHandleDoesNotSendNotificationWhenConfigIsFalse(): void
    {
        Config::set('threads.listeners.comment_created', false);

        $thread = Thread::factory()->create();
        $comment = Comment::factory()->create(['thread_id' => $thread->id]);

        Notification::fake();

        event(new CommentCreated($comment));

        Notification::assertNothingSent();
    }
}