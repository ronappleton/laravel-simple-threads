<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Carbon\Carbon;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    public function testDestroyWithPermissionAccepted(): void
    {
        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $adminUser = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.delete']);
        $adminUser->givePermissionTo($permission);

        $response = $this->actingAs($adminUser)->json('delete', route('threads.destroy', [$thread->id]));

        $response->assertAccepted();

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'user_id' => $user->id,
            'deleted_at' => Carbon::now(),
        ]);
    }

    public function testDestroyThreadWhenReportsExistIsForbidden(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        ThreadReport::factory()->create([
            'thread_id' => $thread->id
        ]);

        $response = $this->actingAs($user)->json('delete', route('threads.destroy', [$thread->id]));

        $response->assertForbidden();

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'deleted_at' => null]);
    }

    public function testDestroyThreadWhenUserIsOwnerAccepted(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->json('delete', route('threads.destroy', [$thread->id]));

        $response->assertAccepted();

        $this->assertDatabaseMissing('threads', ['id' => $thread->id, 'deleted_at' => null]);
    }

    public function testDestroyThreadWhenUserIsNotOwnerForbidden(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();
        $otherUser = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($otherUser)->json('delete', route('threads.destroy', [$thread->id]));

        $response->assertForbidden();

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'deleted_at' => null]);
    }
}