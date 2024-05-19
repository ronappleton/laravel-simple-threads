<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\Threads\Models\Thread;
use Carbon\Carbon;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class HideTest extends TestCase
{
    public function testHideThreadWithPermissionIsAccepted(): void
    {
        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $adminUser = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.hide']);
        $adminUser->givePermissionTo($permission);

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'hidden_at' => null,
        ]);

        $response = $this->actingAs($adminUser)->json('post', route('threads.hide', [$thread->id]));

        $response->assertAccepted();

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'hidden_at' => Carbon::now(),
        ]);
    }

    public function testHideThreadWhenUserIsOwnerIsAccepted(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.hide', [$thread->id]));

        $response->assertAccepted();

        $this->assertDatabaseHas('threads', ['id' => $thread->id, 'hidden_at' => now()]);
    }

    public function testHideThreadWhenUserIsNotOwnerIsForbidden(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();
        $otherUser = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.hide', [$thread->id]));

        $response->assertForbidden();

        $this->assertDatabaseMissing('threads', ['id' => $thread->id, 'hidden_at' => now()]);
    }
}