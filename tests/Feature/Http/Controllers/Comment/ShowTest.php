<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Comment;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\SpatieLaravelPermissionMock\Models\User;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Tests\TestCase;

class ShowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('threads.user_model', User::class);
    }

    public function testShowCommentWithPermissionOk(): void
    {
        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $adminUser = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.comments.show']);
        $adminUser->givePermissionTo($permission);

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

        $response = $this->actingAs($adminUser)->json('get', route('threads.comment.show', [$comment->id]));

        $response->assertOk();
    }

    public function testShowCommentIfThreadReportExistsForThreadIsForbidden(): void
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

        $threadReport = ThreadReport::factory()->create([
            'thread_id' => $thread->id,
        ]);

        $this->assertDatabaseHas('thread_reports', [
            'id' => $threadReport->id,
            'thread_id' => $thread->id,
        ]);

        $response = $this->actingAs($user)->json('get', route('threads.comment.show', [$comment->id]));

        $response->assertForbidden();
    }

    public function testShowCommentOk()
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

        $response = $this->json('get', route('threads.comment.show', [$comment->id]));

        $response->assertOk();
    }
}
