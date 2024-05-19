<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\Threads\Events\ReportResolved;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class ResolveReportTest extends TestCase
{
    public function testResolveThreadReportWithPermissionIsAccepted(): void
    {
        Event::fake(ReportResolved::class);

        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $adminUser = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.report.resolve']);

        $adminUser->givePermissionTo($permission);

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $threadReport = $thread->reports()->create([
            'user_id' => $user->id,
            'reason' => 'This is a reason',
        ]);

        $response = $this->actingAs($adminUser)->json('post', route('threads.report.resolve', [$threadReport->id]));

        $response->assertAccepted();

        $this->assertDatabaseHas('thread_reports', [
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'reason' => 'This is a reason',
            'resolved_at' => Carbon::now(),
            'deleted_at' => Carbon::now(),
        ]);

        Event::assertDispatched(ReportResolved::class, function ($event) use ($thread) {
            return $event->getReport()->thread->id === $thread->id;
        });
    }

    public function testResolveThreadReportWhenUnauthenticatedIsForbidden(): void
    {
        $thread = Thread::factory()->create([
            'threaded_id' => $this->getNewThreaded()->id,
            'threaded_type' => $this->getNewThreaded()::class,
            'user_id' => $this->getNewUser()->id,

        ]);

        $threadReport = ThreadReport::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $this->getNewUser()->id,
            'reason' => 'This is a reason',
        ]);

        $response = $this->json('post', route('threads.report.resolve', [$threadReport->id]));

        $response->assertForbidden();
    }

    public function testResolveThreadReportWithoutPermissionIsForbidden(): void
    {
        $thread = Thread::factory()->create([
            'threaded_id' => $this->getNewThreaded()->id,
            'threaded_type' => $this->getNewThreaded()::class,
            'user_id' => $this->getNewUser()->id,
        ]);

        $threadReport = ThreadReport::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $this->getNewUser()->id,
            'reason' => 'This is a reason',
        ]);

        $response = $this->actingAs($this->getNewUser())->json('post', route('threads.report.resolve', [$threadReport->id]));

        $response->assertForbidden();
    }
}
