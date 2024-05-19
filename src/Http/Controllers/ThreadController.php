<?php

declare(strict_types=1);

namespace Appleton\Threads\Http\Controllers;

use Appleton\Threads\Http\Requests\CreateThreadReportRequest;
use Appleton\Threads\Http\Requests\CreateThreadRequest;
use Appleton\Threads\Http\Requests\UpdateThreadRequest;
use Appleton\Threads\Http\Resources\ThreadResource;
use Appleton\Threads\Models\Thread;
use Appleton\Threads\Models\ThreadReport;
use Appleton\Threads\Services\ThreadService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class ThreadController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly ThreadService $threadService)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->integer('per_page', 10);

        $threads = Thread::where('hidden_at', null)
            ->paginate($perPage);

        return ThreadResource::collection($threads);
    }

    public function store(CreateThreadRequest $request): Response
    {
        $this->authorize('create', Thread::class);

        $this->threadService->createThread($request);

        return response()->noContent(Response::HTTP_CREATED);
    }

    public function show(string $thread): ThreadResource
    {
        $thread = Thread::findOrFail($thread);

        $this->authorize('show', $thread);

        return ThreadResource::make($thread);
    }

    public function update(UpdateThreadRequest $request, Thread $thread): Response
    {
        $this->authorize('update', $thread);

        $this->threadService->updateThread($thread, $request);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    public function destroy(Thread $thread): Response
    {
        $this->authorize('delete', $thread);

        $this->threadService->deleteThread($thread);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    public function restore(string $id): Response
    {
        $thread = Thread::withTrashed()->findOrFail($id);

        $this->authorize('restore', $thread);

        $this->threadService->restoreThread($id);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    public function toggleLock(Thread $thread): Response
    {
        $this->authorize('lock', $thread);

        $thread->locked_at === null
            ? $this->threadService->lockThread($thread)
            : $this->threadService->unlockThread($thread);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    public function togglePin(Thread $thread): Response
    {
        $this->authorize('pin', $thread);

        $thread->pinned_at === null
            ? $this->threadService->pinThread($thread)
            : $this->threadService->unpinThread($thread);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    public function toggleHide(Thread $thread): Response
    {
        $this->authorize('hide', $thread);

        $thread->hidden_at === null
            ? $this->threadService->hideThread($thread)
            : $this->threadService->unHideThread($thread);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    public function toggleLike(Thread $thread): Response
    {
        $thread->likes()->where('user_id', auth()->id())->exists()
            ? $this->threadService->unlikeThread($thread)
            : $this->threadService->likeThread($thread);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    public function report(CreateThreadReportRequest $request, string $id): Response
    {
        $thread = Thread::findOrFail($id);

        $this->threadService->reportThread($request, $thread);

        return response()->noContent(Response::HTTP_CREATED);
    }

    public function resolveReport(ThreadReport $threadReport): Response
    {
        $this->authorize('resolveReport', $threadReport);

        $this->threadService->resolveThreadReport($threadReport);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }
}
