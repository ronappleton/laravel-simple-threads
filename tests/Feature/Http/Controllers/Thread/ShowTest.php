<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Tests\TestCase;

class ShowTest extends TestCase
{
    public function testShowThreadWithPermissionOk(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $adminUser = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.comments.show']);
        $adminUser->givePermissionTo($permission);

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($adminUser)->json('get', route('threads.show', [$thread->id]));

        $response->assertOk();
    }

    public function testShowThreadWithReportsForbidden(): void
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
            'user_id' => $this->getNewUser()->id,
        ]);

        $this->assertDatabaseHas('thread_reports', [
            'id' => $threadReport->id,
            'thread_id' => $thread->id,
        ]);

        $response = $this->json('get', route('threads.show', [$thread->id]));

        $response->assertForbidden();
    }

    public function testShowThreadWhenAuthenticatedIsOk(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->json('get', route('threads.show', [$thread->id]));

        $response->assertOk();
    }

    public function testShowThreadWhenUnAuthenticatedIsOk(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $response = $this->json('get', route('threads.show', [$thread->id]));

        $response->assertOk();
    }
}
