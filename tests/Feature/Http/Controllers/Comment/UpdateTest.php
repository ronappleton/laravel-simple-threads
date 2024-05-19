<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Comment;

use Appleton\SpatieLaravelPermissionMock\Models\User;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Carbon\Carbon;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('threads.user_model', User::class);
    }

    public function testUpdateCommentIfThreadReportedForbidden()
    {
        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'content' => 'This is a thread',
        ]);

        ThreadReport::factory()->create([
            'thread_id' => $thread->id,
        ]);

        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $response = $this->actingAs($user)->json('patch', route('threads.comment.update', [$comment->id]), [
            'content' => 'This is a comment',
        ]);

        $response->assertForbidden();
    }

    public function testUpdateCommentIfCommentReportedForbidden()
    {
        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $user2 = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user2->id,
            'content' => 'This is a thread',
        ]);

        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $threadReport = ThreadReport::factory()->create([
            'user_id' => $user2->id,
            'comment_id' => $comment->id,
        ]);

        $response = $this->actingAs($user)->json('patch', route('threads.comment.update', [$comment->id]), [
            'content' => 'This is a comment',
        ]);

        $response->assertForbidden();
    }

    public function testUpdateCommentIfThreadLockedForbidden()
    {
        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $user2 = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user2->id,
            'content' => 'This is a thread',
            'locked_at' => Carbon::now(),
        ]);

        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $response = $this->actingAs($user)->json('patch', route('threads.comment.update', [$comment->id]), [
            'content' => 'This is a comment',
        ]);

        $response->assertForbidden();
    }

    public function testUpdateCommentIfNotCreatorForbidden()
    {
        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $user2 = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user2->id,
            'content' => 'This is a thread',
        ]);

        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user2->id,
            'content' => 'This is a comment',
        ]);

        $response = $this->actingAs($user)->json('patch', route('threads.comment.update', [$comment->id]), [
            'content' => 'This is a comment',
        ]);

        $response->assertForbidden();
    }

    public function testUpdateCommentAccepted()
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

        $response = $this->actingAs($user)->json('patch', route('threads.comment.update', [$comment->id]), [
            'content' => 'This is a comment',
        ]);

        $response->assertAccepted();
    }
}
