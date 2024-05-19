<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Models\Thread;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    public function testCanGetThreadedModel(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $fetchedThreaded = $thread->threaded;

        $this->assertEquals($threaded->id, $fetchedThreaded->id);
    }

    public function testCanGetUser(): void
    {
        config()->set('threads.user_model', UserUuid::class);

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $fetchedUser = $thread->user;

        $this->assertEquals($user->id, $fetchedUser->id);
    }
}