<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Appleton\Threads\Models\Thread;
use Tests\TestCase;

class UnLikeTest extends TestCase
{
    public function testUnlike(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $thread->likes()->create(['user_id' => $user->id]);


        $response = $this->actingAs($user)->json('post', route('threads.like', [$thread->id]));

        $response->assertStatus(202);

        $this->assertDatabaseMissing('thread_likes', [
            'thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);
    }
}