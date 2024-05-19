<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Comment;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\SpatieLaravelPermissionMock\Models\User;
use Tests\TestCase;
use Appleton\Threads\Models\Thread;
use Carbon\Carbon;
use Appleton\Threads\Models\Comment;

class DestroyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('threads.user_model', User::class);
    }

    public function testDestroyCommentWithPermissionOk()
    {
        $user = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.comments.delete']);
        $user->givePermissionTo('threads.comments.delete');

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

        $response = $this->actingAs($user)->json('delete', route('threads.comment.destroy', [$comment->id]));

        $response->assertNoContent();
    }

    public function testDestroyCommentWhenThreadReportExistsIsForbidden(): void
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

        $thread->reports()->create([
            'user_id' => $user->id,
            'reason' => 'This is a reason',
        ]);

        $response = $this->actingAs($user)->json('delete', route('threads.comment.destroy', [$comment->id]));

        $response->assertForbidden();
    }

    public function testCommentCannotBeDestroyedIfReported(): void
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

        $comment->reports()->create([
            'user_id' => $user->id,
            'reason' => 'This is a reason',
        ]);

        $response = $this->actingAs($user)->json('delete', route('threads.comment.destroy', [$comment->id]));

        $response->assertForbidden();
    }

    public function testDestroyCommentIfUserNotOwnerForbidden()
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

        $response = $this->actingAs($user)->json('delete', route('threads.comment.destroy', [$comment->id]));

        $response->assertForbidden();
    }

    public function testDestroyCommentIfUserCanDeleteAllowed()
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

        $response = $this->actingAs($user)->json('delete', route('threads.comment.destroy', [$comment->id]));

        $response->assertNoContent();
    }
}