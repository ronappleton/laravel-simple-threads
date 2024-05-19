<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\Threads\Events\ThreadUnlocked;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class UnLockTest extends TestCase
{
    public function testUnlockThreadWithPermissionAccepted(): void
    {
        Event::fake(ThreadUnlocked::class);

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $adminUser = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.lock']);
        $adminUser->givePermissionTo($permission);

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'locked_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($adminUser)->json('post', route('threads.lock', [$thread->id]));

        $response->assertAccepted();

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'locked_at' => null,
        ]);

        Event::assertDispatched(ThreadUnlocked::class, function ($event) use ($thread) {
            return $event->getThread()->id === $thread->id;
        });
    }
    public function testUnlockThreadIfUserIsOwnerAndReportExistsIsForbidden(): void
    {
        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'locked_at' => Carbon::now(),
        ]);

        $threadReport = ThreadReport::factory()->create([
            'thread_id' => $thread->id,
        ]);

        $this->assertDatabaseHas('thread_reports', [
            'id' => $threadReport->id,
            'thread_id' => $thread->id,
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.lock', [$thread->id]));

        $response->assertForbidden();

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'locked_at' => Carbon::now()
        ]);
    }

    public function testUnlockWhenUserIsOwnerAccepted(): void
    {
        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'locked_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.lock', [$thread->id]));

        $response->assertAccepted();

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'locked_at' => null,
        ]);
    }

    public function testUnlockIfUserIsNotOwnerIsForbidden(): void
    {
        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();
        $otherUser = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'locked_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($otherUser)->json('post', route('threads.lock', [$thread->id]));

        $response->assertForbidden();

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'locked_at' => Carbon::now(),
        ]);
    }
}