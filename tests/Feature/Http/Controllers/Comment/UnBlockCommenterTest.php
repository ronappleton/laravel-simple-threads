<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Comment;

use Appleton\SpatieLaravelPermissionMock\Models\PermissionUuid;
use Appleton\SpatieLaravelPermissionMock\Models\UserUuid;
use Appleton\Threads\Models\BlockedCommenter;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\Comment;
use Carbon\Carbon;
use Tests\TestCase;

class UnBlockCommenterTest extends TestCase
{
    public function testUnBlockCommenterWithPermissionIsAccepted(): void
    {
        config()->set('threads.user_model', UserUuid::class);

        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $adminUser = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.commenter.unblock']);
        $adminUser->givePermissionTo($permission);

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'hidden_at' => Carbon::now(),
        ]);

        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'This is a comment',
            'hidden_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($adminUser)->json('post', route('threads.commenter.unblock', [$user->id]),[
            'unblock_reason' => 'This is the reason',
        ]);

        $response->assertAccepted();

        $this->assertDatabaseMissing('blocked_commenters', [
            'blocker_user_id' => $adminUser->id,
            'blocked_user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'hidden_at' => null,
        ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'hidden_at' => null,
        ]);
    }

    public function testBlockCommenterWithoutPermissionIsForbidden(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $adminUser = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'hidden_at' => Carbon::now(),
        ]);

        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'This is a comment',
            'hidden_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($adminUser)->json('post', route('threads.commenter.unblock', [$user->id]), [
            'unblock_reason' => 'This is the reason',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('blocked_commenters', [
            'blocker_user_id' => $adminUser->id,
            'blocked_user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'hidden_at' => Carbon::now(),
        ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'hidden_at' => Carbon::now(),
        ]);
    }

    public function testBlockCommenterWhenUnauthenticatedIsForbidden(): void
    {
        $threaded = $this->getNewThreaded();
        $user = $this->getNewUser();

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $user->id,
            'hidden_at' => Carbon::now(),
        ]);

        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'This is a comment',
            'hidden_at' => Carbon::now(),
        ]);

        $response = $this->json('post', route('threads.commenter.unblock', [$user->id]), [
            'unblock_reason' => 'This is the reason',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('blocked_commenters', [
            'blocker_user_id' => null,
            'blocked_user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'hidden_at' => Carbon::now(),
        ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'hidden_at' => Carbon::now(),
        ]);
    }

    public function testUnBlockCommenterWhenTryingToUnblockOwnUserIsForbidden(): void
    {
        config()->set('threads.user_model', UserUuid::class);

        $threaded = $this->getNewThreaded();

        $adminUser = $this->getNewUser();
        $permission = PermissionUuid::create(['name' => 'threads.commenter.unblock']);
        $adminUser->givePermissionTo($permission);

        $thread = Thread::factory()->create([
            'threaded_id' => $threaded->id,
            'threaded_type' => $threaded::class,
            'user_id' => $adminUser->id,
            'hidden_at' => Carbon::now(),
        ]);

        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $adminUser->id,
            'content' => 'This is a comment',
            'hidden_at' => Carbon::now(),
        ]);

        $blockedCommenter = BlockedCommenter::factory()->create([
            'blocker_user_id' => $adminUser->id,
            'blocked_user_id' => $adminUser->id,
        ]);

        $response = $this->actingAs($adminUser)->json('post', route('threads.commenter.unblock', [$adminUser->id]), [
            'unblock_reason' => 'This is the reason',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('blocked_commenters', [
            'blocker_user_id' => $adminUser->id,
            'blocked_user_id' => $adminUser->id,
        ]);

        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'hidden_at' => Carbon::now(),
        ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'hidden_at' => Carbon::now(),
        ]);
    }
}