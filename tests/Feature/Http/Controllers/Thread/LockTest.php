<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\Threads\Events\ThreadLocked;
use Appleton\Threads\Listeners\ThreadLockedListener;
use Appleton\Threads\Models\Thread;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class LockTest extends TestCase
{
    public function testLockThreadWithPermissionIsAccepted(): void
    {
        Event::fake(ThreadLocked::class);

        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $adminUser = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.lock']);
        $adminUser->givePermissionTo($permission);

        config()->set('threads.user_model', $user::class);

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($adminUser)->json('post', route('threads.lock', [$thread->id]));

        $response->assertAccepted();

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'locked_at' => Carbon::now(),
        ]);

        Event::assertDispatched(ThreadLocked::class, function ($event) use ($thread) {
            return $event->getThread()->id === $thread->id;
        });
    }

    public function testLockThreadWhenUserIsOwnerIsAccepted(): void
    {
        Event::fake(ThreadLocked::class);

        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        Event::assertListening(ThreadLocked::class, ThreadLockedListener::class);

        $response = $this->actingAs($user)->json('post', route('threads.lock', [$thread->id]));

        $response->assertAccepted();

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'locked_at' => Carbon::now(),
        ]);

        Event::assertDispatched(ThreadLocked::class, function ($event) use ($thread) {
            return $event->getThread()->id === $thread->id;
        });
    }

    public function testLockThreadWhenUserIsNotOwnerIsForbidden(): void
    {
        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();
        $otherUser = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($otherUser)->json('post', route('threads.lock', [$thread->id]));

        $response->assertForbidden();

        $this->assertDatabaseMissing('threads', [
            'id' => $thread->id,
            'locked_at' => Carbon::now(),
        ]);
    }
}
