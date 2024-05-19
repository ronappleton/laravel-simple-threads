<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Comment;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Carbon\Carbon;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class ResolveReportTest extends TestCase
{
    public function testResolveCommentReportWithPermissionIsAccepted(): void
    {
        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $adminUser = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.report.resolve']);

        $adminUser->givePermissionTo($permission);

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $threadReport = $comment->reports()->create([
            'user_id' => $user->id,
            'reason' => 'This is a reason',
        ]);

        $response = $this->actingAs($adminUser)->json('post', route('threads.report.resolve', [$threadReport->id]));

        $response->assertAccepted();

        $this->assertDatabaseHas('thread_reports', [
            'comment_id' => $comment->id,
            'user_id' => $user->id,
            'reason' => 'This is a reason',
            'resolved_at' => Carbon::now(),
            'deleted_at' => Carbon::now(),
        ]);
    }

    public function testResolveThreadReportWhenUnauthenticatedIsForbidden(): void
    {
        $thread = Thread::factory()->create([
            'threaded_id' => $this->getNewThreaded()->id,
            'threaded_type' => $this->getNewThreaded()::class,
            'user_id' => $this->getNewUser()->id,
        ]);

        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $this->getNewUser()->id,
            'content' => 'This is a comment',
        ]);

        $threadReport = ThreadReport::factory()->create([
            'comment_id' => $comment->id,
            'user_id' => $this->getNewUser()->id,
            'reason' => 'This is a reason',
        ]);

        $response = $this->json('post', route('threads.report.resolve', [$threadReport->id]));

        $response->assertForbidden();
    }

    public function testResolveThreadReportWithoutPermissionIsForbidden(): void
    {
        $thread = Thread::factory()->create([
            'threaded_id' => $this->getNewThreaded()->id,
            'threaded_type' => $this->getNewThreaded()::class,
            'user_id' => $this->getNewUser()->id,
        ]);

        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $this->getNewUser()->id,
            'content' => 'This is a comment',
        ]);

        $threadReport = ThreadReport::factory()->create([
            'comment_id' => $comment->id,
            'user_id' => $this->getNewUser()->id,
            'reason' => 'This is a reason',
        ]);

        $response = $this->actingAs($this->getNewUser())->json('post', route('threads.report.resolve', [$threadReport->id]));

        $response->assertForbidden();
    }
}