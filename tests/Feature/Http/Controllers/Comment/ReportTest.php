<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Comment;

use Appleton\SpatieLaravelPermissionMock\Models\User;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Carbon\Carbon;
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
            'created_at' => Carbon::now(),
        ]);
    }

    public function testReportingCommentAuthenticatedAccepted(): void
    {
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