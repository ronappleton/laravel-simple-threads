<?php

declare(strict_types=1);

namespace Appleton\Threads\Http\Resources;

use Appleton\Threads\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Thread */
class ThreadResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'creator' => $this->user,
            'title' => $this->title,
            'content' => $this->content,
            'locked_at' => $this->locked_at,
            'pinned_at' => $this->pinned_at,
            'hidden_at' => $this->hidden_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'comment_count' => $this->comment_count,
            'like_count' => $this->like_count,
            'comments_count' => $this->comments_count,
            'reported_at' => $this->reported_at,
        ];
    }
}
