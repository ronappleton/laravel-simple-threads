<?php

declare(strict_types=1);

namespace Appleton\Threads\Http\Controllers;

use Appleton\Threads\Http\Requests\CreateBlockCommenterRequest;
use Appleton\Threads\Http\Requests\CreateCommentRequest;
use Appleton\Threads\Http\Requests\CreateThreadReportRequest;
use Appleton\Threads\Http\Requests\UnblockCommenterRequest;
use Appleton\Threads\Http\Requests\UpdateCommentRequest;
use Appleton\Threads\Http\Resources\CommentResource;
use Appleton\Threads\Models\Comment;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Services\ThreadService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly ThreadService $threadService)
    {
    }

    public function store(CreateCommentRequest $request, Thread $thread): Response
    {
        $this->authorize('create', [Comment::class, $thread]);

        $this->threadService->createComment($thread, $request);

        return response()->noContent(Response::HTTP_CREATED);
    }

    public function show(string $comment): CommentResource
    {
        $comment = Comment::findOrFail($comment);

        $this->authorize('show', $comment);

        return CommentResource::make($comment);
    }

    public function update(UpdateCommentRequest $request, Comment $comment): Response
    {
        $this->authorize('update', $comment);

        $this->threadService->updateComment($comment, $request);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    public function destroy(Comment $comment): Response
    {
        $this->authorize('delete', $comment);

        $this->threadService->deleteComment($comment);

        return response()->noContent(Response::HTTP_NO_CONTENT);
    }

    public function toggleHide(string $id): Response
    {
        $comment = Comment::withTrashed()->findOrFail($id);

        $this->authorize('hide', $comment);

        $comment->hidden_at
            ? $this->threadService->unHideComment($comment)
            : $this->threadService->hideComment($comment);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    public function restore(string $id): Response
    {
        $comment  = Comment::withTrashed()->findOrFail($id);

        $this->authorize('restore', $comment);

        $this->threadService->restoreComment($comment);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    public function report(CreateThreadReportRequest $request, string $id): Response
    {
        $comment = Comment::findOrFail($id);

        $this->threadService->reportComment($request, $comment);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    public function blockCommenter(CreateBlockCommenterRequest $request, string $user): Response
    {
        $this->authorize('block', [Comment::class, $user]);

        $this->threadService->blockCommenter($request, $user);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    public function unBlockCommenter(UnblockCommenterRequest $request, string $user): Response
    {
        $this->authorize('unblock', [Comment::class, $user]);

        $this->threadService->unBlockCommenter($request, $user);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }
}
