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

    public function testCanGetDeepThreadedRelationsTwoDeep(): void
    {
        config()->set('threads.user_model', UserUuid::class);

        $oneDeep = [
            'threaded',
            'user',
        ];

        $threaded = $this->getNewThreaded()->user()->associate($this->getNewUser());
        $threaded->save();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $deepThreadedRelations = $thread->deepNestedRelation($oneDeep);

        $this->assertEquals($threaded->user->id, $deepThreadedRelations->first()->id);
    }

    public function testCanGetDeepThreadedRelationsThreeDeep(): void
    {
        $twoDeep = [
            'threaded',
            'deepThreaded',
            'user',
        ];

        $threaded = $this->getNewThreaded();
        $deepThreaded = $this->getNewThreaded();
        $user = $this->getNewUser();
        $deepThreaded->user()->associate($user)->save();
        $threaded->deepThreaded()->associate($deepThreaded)->save();

        $user2 = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user2->id,
        ]);

        $deepThreadedRelations = $thread->deepNestedRelation($twoDeep);

        $this->assertEquals($threaded->deepThreaded->user->id, $deepThreadedRelations->first()->id);
    }

    public function testCanGetDeepThreadedRelationsFourDeep(): void
    {
        $threeDeep = [
            'threaded',
            'deepThreaded',
            'deepThreaded',
            'user',
        ];

        $threaded = $this->getNewThreaded();
        $deepThreaded = $this->getNewThreaded();
        $deepThreaded2 = $this->getNewThreaded();
        $user = $this->getNewUser();
        $deepThreaded2->user()->associate($user)->save();
        $deepThreaded->deepThreaded()->associate($deepThreaded2)->save();
        $threaded->deepThreaded()->associate($deepThreaded)->save();

        $user2 = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user2->id,
        ]);

        $deepThreadedRelations = $thread->deepNestedRelation($threeDeep);

        $this->assertEquals($threaded->deepThreaded->deepThreaded->user->id, $deepThreadedRelations->first()->id);
    }

    public function testDeepNestedRelationReturnsNullIfRelationDoesNotExist(): void
    {
        $twoDeep = [
            'deepThreaded',
            'user',
        ];

        $threaded = $this->getNewThreaded();
        $deepThreaded = $this->getNewThreaded();
        $user = $this->getNewUser();
        $deepThreaded->user()->associate($user)->save();
        $threaded->deepThreaded()->associate($deepThreaded)->save();

        $user2 = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user2->id,
        ]);

        $deepThreadedRelation = $thread->deepNestedRelation($twoDeep);

        $this->assertNull($deepThreadedRelation);
    }
}
