<?php

declare(strict_types=1);

namespace Tests\Unit\Notifications;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Appleton\Threads\Notifications\ReportReceived;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ReportReceivedTest extends TestCase
{
    private ReportReceived $notification;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('threads.user_model', UserUuid::class);
        Config::set('threads.report_url', 'http://localhost/report');
        Config::set('threads.user_name_field', 'display_name');
        Config::set('threads.threaded_user_relations', [
            [
                'user',
            ],
        ]);
        Config::set('threads.notifications.comment_created.sms.message', 'A :type report has been received');
        Config::set('threads.notifications.comment_created.database.message', 'A :type report has been received');
        Config::set('threads.notifications.comment_created.email.message', 'A :type report has been received');
        Config::set('threads.notifications.comment_created.push.message', 'A :type report has been received');

        $user = new UserUuid();
        $user->setAttribute('display_name', 'Test User');
        $user->setAttribute('avatar', 'some_url');

        $thread = new Thread();
        $thread->setAttribute('id', 'some_string');

        $comment = new Comment();
        $comment->thread_id = 'some_string';
        $comment->setAttribute('thread', $thread);

        $threadReport = new ThreadReport();
        $threadReport->thread_id = 'some_string';
        $threadReport->setAttribute('comment', $comment);
        $threadReport->setAttribute('user', $user);
        $threadReport->setAttribute('comment_id', 'some_comment_id');
        $threadReport->setAttribute('thread', $thread);

        $this->notification = new ReportReceived($threadReport);
    }

    public function testVonageMessageIsCorrect(): void
    {
        $message = $this->notification->toVonage(new \stdClass());

        $this->assertInstanceOf(VonageMessage::class, $message);
        $this->assertSame('A comment report has been received - http://localhost/report?report=some_string', $message->content);
    }

    public function testVonageMessageIsCorrectWhenTheReportIsForAThread(): void
    {
        $user = new UserUuid();
        $user->setAttribute('display_name', 'Test User');
        $user->setAttribute('avatar', 'some_url');

        $thread = new Thread();
        $thread->setAttribute('id', 'some_string');

        $comment = new Comment();
        $comment->thread_id = 'some_string';
        $comment->setAttribute('thread', $thread);

        $threadReport = new ThreadReport();
        $threadReport->thread_id = 'some_string';
        $threadReport->setAttribute('user', $user);
        $threadReport->setAttribute('thread', $thread);

        $this->notification = new ReportReceived($threadReport);

        $message = $this->notification->toVonage(new \stdClass());

        $this->assertInstanceOf(VonageMessage::class, $message);
        $this->assertSame('A thread report has been received - http://localhost/report?report=some_string', $message->content);
    }

    public function testDatabaseMessageIsCorrect(): void
    {
        $message = $this->notification->toDatabase(new \stdClass());

        $this->assertIsArray($message);
        $this->assertSame('A comment report has been received', $message['message']);
        $this->assertSame('http://localhost/report?report=some_string', $message['url']);
    }

    public function testBroadcastMessageIsCorrect(): void
    {
        $message = $this->notification->toBroadcast(new \stdClass());

        $this->assertInstanceOf(BroadcastMessage::class, $message);
        $this->assertIsArray($message->data);

        $data = $message->data;

        $this->assertSame('A comment report has been received', $data['message']);
        $this->assertSame('http://localhost/report?report=some_string', $data['url']);
    }

    public function testMailMessageIsCorrect(): void
    {
        $message = $this->notification->toMail(new \stdClass());

        $this->assertInstanceOf(MailMessage::class, $message);

        $data = $message->toArray();

        $this->assertSame('info', $data['level']);
        $this->assertSame('New Report', $data['subject']);
        $this->assertSame('A comment report has been received', $data['introLines'][0]);
        $this->assertSame('View Report', $data['actionText']);
        $this->assertSame('http://localhost/report?report=some_string', $data['actionUrl']);
        $this->assertSame('http://localhost/report?report=some_string', $data['displayableActionUrl']);
    }

    public function testArrayMessageIsCorrect(): void
    {
        $message = $this->notification->toArray(new \stdClass());

        $this->assertIsArray($message);
        $this->assertSame('A comment report has been received', $message['message']);
        $this->assertSame('http://localhost/report?report=some_string', $message['url']);
    }
}
