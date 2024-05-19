<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Comment;

use Appleton\SpatieLaravelPermissionMock\Models\User;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Carbon\Carbon;
use Tests\TestCase;

class RestoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('threads.user_model', User::class);
    }

    public function testRestoreCommentIfThreadHasBeenReportedForbidden(): void
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

        $thread->reports()->create([
            'user_id' => $user2->id,
            'reason' => 'This is a reason',
        ]);

        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'This is a comment',
            'deleted_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.comment.restore', [$comment->id]));

        $response->assertForbidden();
    }

    public function testRestoreIfCommentHasBeenReportedForbidden(): void
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
            'deleted_at' => Carbon::now(),
        ]);

        $comment->reports()->create([
            'user_id' => $user2->id,
            'reason' => 'This is a reason',
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.comment.restore', [$comment->id]));

        $response->assertForbidden();
    }

    public function testRestoreCommentIfThreadLockedForbidden()
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
            'deleted_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.comment.restore', [$comment->id]));

        $response->assertForbidden();
    }

    public function testRestoreCommentWhenUserIsOwnerIsAccepted(): void
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
            'deleted_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.comment.restore', [$comment->id]));

        $response->assertAccepted();
    }

    public function testRestoreCommentWhenUserIsNotOwnerIsForbidden(): void
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
            'deleted_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.comment.restore', [$comment->id]));

        $response->assertForbidden();
    }
}