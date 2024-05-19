<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function testUpdateWhenThreadHasReportsIsForbidden(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        ThreadReport::factory()->create([
            'user_id' => $this->getNewUser()->id,
            'thread_id' => $thread->id,
        ]);

        $response = $this->actingAs($user)->json('patch', route('threads.update', [$thread->id]), [
            'title' => 'Thread Title',
            'content' => 'Thread Content',
        ]);

        $response->assertForbidden();
    }

    public function testUpdateWhenUserIsOwnerAccepted(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->json('patch', route('threads.update', [$thread->id]), [
            'title' => 'Thread Title',
            'content' => 'Thread Content',
        ]);

        $response->assertAccepted();
    }

    public function testUpdateWhenUserIsNotOwnerIsForbidden(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $this->getNewUser()->id,
        ]);

        $response = $this->actingAs($user)->json('patch', route('threads.update', [$thread->id]), [
            'title' => 'Thread Title',
            'content' => 'Thread Content',
        ]);

        $response->assertForbidden();
    }
}