<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Comment;

use Appleton\SpatieLaravelPermissionMock\Models\User;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Carbon\Carbon;
use Tests\TestCase;

class StoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('threads.user_model', User::class);
    }

    public function testStoreCommentWhenThreadReportExistsIsForbidden(): void
    {
        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $threadReport = ThreadReport::factory()->create([
            'thread_id' => $thread->id,
        ]);

        $this->assertDatabaseHas('thread_reports', [
            'id' => $threadReport->id,
            'thread_id' => $thread->id,
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.comment.store', [$thread->id]), [
            'content' => 'This is a comment',
        ]);

        $response->assertForbidden();
    }

    public function testCreateCommentWhenThreadLockedForbidden(): void
    {
        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'content' => 'This is a comment',
            'locked_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.comment.store', [$thread->id]), [
            'content' => 'This is a comment',
        ]);

        $response->assertForbidden();
    }

    public function testCreateCommentUnauthenticatedForbidden()
    {
        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $response = $this->json('post', route('threads.comment.store', [$thread->id]), [
            'content' => 'This is a comment',
        ]);

        $response->assertForbidden();
    }

    public function testCreateCommentAuthenticatedCreated()
    {
        $user = $this->getNewUser();
        $threaded = $this->getNewThreaded();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.comment.store', [$thread->id]), [
            'content' => 'This is a comment',
        ]);

        $response->assertCreated();
    }


}