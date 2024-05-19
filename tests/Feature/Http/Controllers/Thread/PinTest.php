<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Carbon\Carbon;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class PinTest extends TestCase
{
    public function testPinThreadWhenReportExistsIsForbidden(): void
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

        $this->assertDatabaseHas('thread_reports', [
            'id' => $threadReport->id,
            'thread_id' => $thread->id,
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.pin', [$thread->id]));

        $response->assertForbidden();
    }

    public function testPinThreadWhenUserIsOwnerAccepted(): void
    {
        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.pin', [$thread->id]));

        $response->assertAccepted();

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'pinned_at' => Carbon::now(),
        ]);
    }
}
