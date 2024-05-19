<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\Threads\Events\ReportReceived;
use Appleton\Threads\Models\Thread;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class ReportTest extends TestCase
{
    public function testReport(): void
    {
        Event::fake(ReportReceived::class);

        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $adminUser = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.report']);

        $adminUser->givePermissionTo($permission);

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->json('post', route('threads.report', [$thread->id]), [
            'reason' => 'This is a reason',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('thread_reports', [
            'user_id' => $user->id,
            'thread_id' => $thread->id,
            'reason' => 'This is a reason',
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        Event::assertDispatched(ReportReceived::class, function ($event) use ($thread) {
            return $event->getReport()->thread->id === $thread->id;
        });
    }

    public function testReportUnauthenticated(): void
    {
        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $adminUser = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.report']);

        $adminUser->givePermissionTo($permission);

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $response = $this->json(
            'post',
            route('threads.report', [$thread->id]),
            [
                'reason' => 'This is a reason',
            ]
        );

        $response->assertCreated();

        $this->assertDatabaseHas('thread_reports', [
            'thread_id' => $thread->id,
            'reason' => 'This is a reason',
            'created_at' => Carbon::now(),
        ]);
    }
}