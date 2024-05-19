<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Comment;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Events\CommenterBlocked;
use Appleton\Threads\Models\Thread;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class BlockCommenterTest extends TestCase
{
    public function testBlockCommenterWithPermissionIsAccepted(): void
    {
        Event::fake(CommenterBlocked::class);

        config()->set('threads.user_model', UserUuid::class);

        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $adminUser = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.commenter.block']);
        $adminUser->givePermissionTo($permission);

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $comment = $thread->comments()->create([
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $response = $this->actingAs($adminUser)->json('post', route('threads.commenter.block', [$user->id]), [
            'reason' => 'This is a reason',
            'is_permanent' => true,
        ]);

        $response->assertAccepted();

        $this->assertDatabaseHas('blocked_commenters', [
            'blocker_user_id' => $adminUser->id,
            'blocked_user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'hidden_at' => Carbon::now(),
        ]);

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'hidden_at' => Carbon::now(),
        ]);

        Event::assertDispatched(CommenterBlocked::class, function ($event) use ($user) {
            return $event->getBlockedCommenter()->blockedUser->id === $user->id;
        });
    }

    public function testBlockCommenterWithoutPermissionIsForbidden(): void
    {
        config()->set('threads.user_model', UserUuid::class);

        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $adminUser = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $comment = $thread->comments()->create([
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $response = $this->actingAs($adminUser)->json('post', route('threads.commenter.block', [$user->id]), [
            'reason' => 'This is a reason',
            'is_permanent' => true,
        ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('blocked_commenters', [
            'blocker_user_id' => $adminUser->id,
            'blocked_user_id' => $user->id,
        ]);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
            'hidden_at' => Carbon::now(),
        ]);

        $this->assertDatabaseMissing('threads', [
            'id' => $thread->id,
            'hidden_at' => Carbon::now(),
        ]);
    }

    public function testBlockCommenterUnathenticatedIsForbidden(): void
    {
        config()->set('threads.user_model', UserUuid::class);

        TestTime::freeze(Carbon::now());

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
        ]);

        $comment = $thread->comments()->create([
            'user_id' => $user->id,
            'content' => 'This is a comment',
        ]);

        $response = $this->json('post', route('threads.commenter.block', [$user->id]), [
            'reason' => 'This is a reason',
            'is_permanent' => true,
        ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('blocked_commenters', [
            'blocker_user_id' => null,
            'blocked_user_id' => $user->id,
        ]);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
            'hidden_at' => Carbon::now(),
        ]);

        $this->assertDatabaseMissing('threads', [
            'id' => $thread->id,
            'hidden_at' => Carbon::now(),
        ]);
    }
}
