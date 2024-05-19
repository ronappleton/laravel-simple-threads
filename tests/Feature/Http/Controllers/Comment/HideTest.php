<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Comment;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\SpatieLaravelPermissionMock\Models\User;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Tests\TestCase;

class HideTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('threads.user_model', User::class);
    }

    public function testHideCommentWithPermissionAccepted(): void
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

        $permission = PermissionUuid::create(['name' => 'threads.comments.hide']);
        $user->givePermissionTo($permission);

        $response = $this->actingAs($user)->json('post', route('threads.comment.hide', [$comment->id]));

        $response->assertAccepted();
        $this->assertNotNull($comment->refresh()->hidden_at);
    }

    public function testHideCommentWhenThreadReportExistsIsForbidden()
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

        $thread->reports()->create([
            'user_id' => $user->id,
            'reason' => 'This is a report',
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.comment.hide', [$comment->id]));

        $response->assertForbidden();
    }

    public function testHideCommentWhenCommentReportExistsIsForbidden()
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

        $comment->reports()->create([
            'user_id' => $user->id,
            'reason' => 'This is a report',
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.comment.hide', [$comment->id]));

        $response->assertForbidden();
    }

    public function testHideCommentIfOwnerAccepted(): void
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

        $response = $this->actingAs($user)->json('post', route('threads.comment.hide', [$comment->id]));

        $response->assertAccepted();
        $this->assertNotNull($comment->refresh()->hidden_at);
    }

    public function testHideCommentIfNotCommentCreatorForbidden(): void
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
        ]);

        $response = $this->actingAs($user2)->json('post', route('threads.comment.hide', [$comment->id]));

        $response->assertForbidden();
    }
}
