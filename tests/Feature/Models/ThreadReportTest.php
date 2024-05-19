<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Tests\TestCase;

class ThreadReportTest extends TestCase
{
    public function testCanGetThread(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $threadReport = ThreadReport::factory()->create([
            'thread_id' => $thread->id,
        ]);

        $fetchedThread = $threadReport->thread;

        $this->assertEquals($thread->id, $fetchedThread->id);
    }

    public function testCanGetUser(): void
    {
        config()->set('threads.user_model', UserUuid::class);

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $threadReport = ThreadReport::factory()->create([
            'user_id' => $user->id,
            'thread_id' => $thread->id,
        ]);

        $fetchedUser = $threadReport->user;

        $this->assertEquals($user->id, $fetchedUser->id);
    }

    public function testCanGetComment(): void
    {
        config()->set('threads.user_model', UserUuid::class);

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

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

        $threadReport = ThreadReport::factory()->create([
            'user_id' => $user->id,
            'comment_id' => $comment->id,
        ]);

        $fetchedComment = $threadReport->comment;

        $this->assertEquals($comment->id, $fetchedComment->id);
    }
}