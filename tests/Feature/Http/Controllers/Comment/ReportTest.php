<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Comment;

use Appleton\SpatieLaravelPermissionMock\Models\User;
use Appleton\Threads\Events\ReportReceived;
use Appleton\Threads\Listeners\ReportReceivedListener;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ReportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('threads.user_model', User::class);
    }

    public function testReportingCommentUnauthenticatedAccepted(): void
    {
        Event::fake(ReportReceived::class);

        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'content' => 'This is a thread',
        ]);

        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $response = $this->json('post', route('threads.comment.report', [$comment->id]), [
            'reason' => 'This is a reason',
        ]);

        $response->assertAccepted();

        $this->assertDatabaseHas('thread_reports', [
            'user_id' => null,
            'comment_id' => $comment->id,
            'reason' => 'This is a reason',
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        Event::assertDispatched(ReportReceived::class, function ($event) use ($comment) {
            return $event->getReport()->comment->id === $comment->id;
        });
    }

    public function testReportingCommentAuthenticatedAccepted(): void
    {
        Event::fake(ReportReceived::class);

        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'content' => 'This is a thread',
        ]);

        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        Event::assertListening(ReportReceived::class, ReportReceivedListener::class);

        $response = $this->actingAs($user)->json('post', route('threads.comment.report', [$comment->id]), [
            'reason' => 'This is a reason',
        ]);

        $response->assertAccepted();

        $this->assertDatabaseHas('thread_reports', [
            'user_id' => $user->id,
            'comment_id' => $comment->id,
            'reason' => 'This is a reason',
            'created_at' => Carbon::now(),
        ]);
    }
}
