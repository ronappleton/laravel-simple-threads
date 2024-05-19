<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Comment;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\SpatieLaravelPermissionMock\Models\User;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Carbon\Carbon;
use Tests\TestCase;

class UnHideTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('threads.user_model', User::class);
    }

    public function testUnHideCommentWithPermissionAccepted(): void
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
            'hidden_at' => Carbon::now(),
        ]);

        $permission = PermissionUuid::create(['name' => 'threads.comments.hide']);
        $user->givePermissionTo($permission);

        $response = $this->actingAs($user)->json('post', route('threads.comment.hide', [$comment->id]));

        $response->assertAccepted();
        $this->assertNull($comment->refresh()->hidden_at);
    }

    public function testUnHideCommentWhenThreadReportExistsIsForbidden()
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
            'hidden_at' => Carbon::now(),
        ]);

        $thread->reports()->create([
            'user_id' => $user2->id,
            'reason' => 'This is a reason',
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.comment.hide', [$comment->id]));

        $response->assertForbidden();
    }

    public function testUnHideCommentWhenCommentReportExistsIsForbidden()
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
            'hidden_at' => Carbon::now(),
        ]);

        $comment->reports()->create([
            'user_id' => $user2->id,
            'reason' => 'This is a reason',
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.comment.hide', [$comment->id]));

        $response->assertForbidden();
    }

    public function testUnHideCommentIfOwnerAccepted(): void
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
            'hidden_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.comment.hide', [$comment->id]));

        $response->assertAccepted();
        $this->assertNull($comment->refresh()->hidden_at);
    }

    public function testUnHideCommentIfNotCommentCreatorForbidden(): void
    {
        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $user2 = $this->getNewUser();

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
            'hidden_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($user2)->json('post', route('threads.comment.hide', [$comment->id]));

        $response->assertForbidden();
    }
}