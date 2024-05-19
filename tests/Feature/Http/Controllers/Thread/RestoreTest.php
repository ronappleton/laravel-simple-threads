<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Carbon\Carbon;
use Tests\TestCase;

class RestoreTest extends TestCase
{
    public function testRestoreWhenReportExistsIsForbidden(): void
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
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('thread_reports', [
            'id' => $threadReport->id,
            'thread_id' => $thread->id,
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.restore', [$thread->id]));

        $response->assertForbidden();
    }

    public function testRestoreWhenUserIsOwnerIsAccepted(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'deleted_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.restore', [$thread->id]));

        $response->assertAccepted();

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'deleted_at' => null,
        ]);
    }

    public function testRestoreWhenUserIsOwnerIsForbidden(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();
        $otherUser = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $otherUser->id,
            'deleted_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.restore', [$thread->id]));

        $response->assertForbidden();

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'deleted_at' => Carbon::now(),
        ]);
    }
}
