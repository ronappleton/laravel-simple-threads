<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadLike;
use Tests\TestCase;

class ThreadLikeTest extends TestCase
{
    public function testCanGetThread(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $threadLike = $thread->likes()->create([
            'user_id' => $user->id,
        ]);

        $fetchedThread = $threadLike->thread;

        $this->assertEquals($thread->id, $fetchedThread->id);
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

        $threadLike = $thread->likes()->create([
            'user_id' => $user->id,
        ]);

        $fetchedUser = $threadLike->user;

        $this->assertEquals($user->id, $fetchedUser->id);
    }
}