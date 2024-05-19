<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Appleton\Threads\Events\ThreadCreated;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class StoreTest extends TestCase
{
    public function testStoreThreadAuthenticatedIsCreated(): void
    {
        Event::fake(ThreadCreated::class);

        $user = $this->getNewUser();

        $response = $this->actingAs($user)->json('post', route('threads.store'), [
            'threaded_id' => '1',
            'threaded_type' => 'App\Models\User',
            'user_id' => $user->id,
            'title' => 'Thread Title',
            'content' => 'Thread Content',
        ]);

        $response->assertCreated();

        Event::assertDispatched(ThreadCreated::class, function ($event) use ($user) {
            return $event->getThread()->user_id === $user->id;
        });
    }

    public function testStoreThreadUnauthenticatedIsForbidden(): void
    {
        $response = $this->json('post', route('threads.store'), [
            'threaded_id' => '1',
            'threaded_type' => 'App\Models\User',
            'user_id' => '1',
            'title' => 'Thread Title',
            'content' => 'Thread Content',
        ]);

        $response->assertForbidden();
    }
}