<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Events\ReportReceived;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Appleton\Threads\Notifications\ReportReceived as ReportReceivedNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use function Psy\debug;

class ReportReceivedListenerTest extends TestCase
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
        Config::set('threads.listeners.report_received', true);
        Config::set('threads.notifications.report_received.sms.enabled', true);
        Config::set('threads.notifications.report_received.database.enabled', true);
        Config::set('threads.notifications.report_received.email.enabled', true);
        Config::set('threads.notifications.report_received.push.enabled', true);

        $adminUser = $this->getNewUser();
        Config::set('threads.moderator_emails', [
            $adminUser->email,
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

        $threadReport = ThreadReport::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);

        Notification::fake();

        event(new ReportReceived($threadReport));

        try {
            Notification::assertSentTo($adminUser, ReportReceivedNotification::class);
        } catch (\Throwable $e) {
            dd($e->getMessage(), debug_backtrace());
        }
    }

    public function testHandleDoesNotSendNotificationWhenConfigIsFalse(): void
    {
        Config::set('threads.listeners.report_received', false);

        $adminUser = $this->getNewUser();
        Config::set('threads.moderator_emails', [
            $adminUser->email,
        ]);

        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $threadReport = ThreadReport::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);

        Notification::fake();

        event(new ReportReceived($threadReport));

        Notification::assertNothingSent();
    }
}