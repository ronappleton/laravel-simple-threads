<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Carbon\Carbon;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class UnHideTest extends TestCase
{
    public function testUnHideWhenWithPermissionIsAccepted(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $adminUser = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.hide']);
        $adminUser->givePermissionTo($permission);

        $thread = Thread::factory()->create([
            'user_id' => $user->id,
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'hidden_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($adminUser)->json('post', route('threads.hide', [$thread->id]));

        $response->assertAccepted();

        $this->assertDatabaseMissing('threads', ['id' => $thread->id, 'hidden_at' => now()]);
    }

    public function testUnHideThreadWhenReportExistsIsForbidden(): void
    {
        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'hidden_at' => Carbon::now(),
        ]);

        $threadReport = ThreadReport::factory()->create([
            'thread_id' => $thread->id,
        ]);

        $this->assertDatabaseHas('thread_reports', [
            'id' => $threadReport->id,
            'thread_id' => $thread->id,
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.hide', [$thread->id]));

        $response->assertForbidden();

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'hidden_at' => Carbon::now(),
        ]);
    }

    public function testUnHideThreadWhenUserIsOwnerIsAccepted(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'hidden_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.hide', [$thread->id]));

        $response->assertAccepted();

        $this->assertDatabaseMissing('threads', ['id' => $thread->id, 'hidden_at' => now()]);
    }

    public function testUnHideThreadWhenUserIsNotOwnerIsForbidden(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();
        $otherUser = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'hidden_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($otherUser)->json('post', route('threads.hide', [$thread->id]));

        $response->assertForbidden();

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'hidden_at' => now()]);
    }
}