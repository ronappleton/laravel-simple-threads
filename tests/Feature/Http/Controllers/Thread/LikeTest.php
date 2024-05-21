<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Appleton\Threads\Events\LikeReceived;
use Appleton\Threads\Listeners\LikeReceivedListener;
use Appleton\Threads\Models\Thread;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LikeTest extends TestCase
{
    public function testLike(): void
    {
        Event::fake(LikeReceived::class);

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        Event::assertListening(LikeReceived::class, LikeReceivedListener::class);

        $response = $this->actingAs($user)->json('post', route('threads.like', [$thread->id]));

        $response->assertStatus(202);

        $this->assertDatabaseHas('thread_likes', ['thread_id' => $thread->id, 'user_id' => $user->id]);

        Event::assertDispatched(LikeReceived::class, function (LikeReceived $event) use ($thread) {
            return $event->getLike()->thread->id === $thread->id;
        });
    }
}
