<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Thread;

use Tests\TestCase;

class StoreTest extends TestCase
{
    public function testStoreThreadAuthenticatedIsCreated(): void
    {
        $user = $this->getNewUser();

        $response = $this->actingAs($user)->json('post', route('threads.store'), [
            'threaded_id' => '1',
            'threaded_type' => 'App\Models\User',
            'user_id' => $user->id,
            'title' => 'Thread Title',
            'content' => 'Thread Content',
        ]);

        $response->assertCreated();
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