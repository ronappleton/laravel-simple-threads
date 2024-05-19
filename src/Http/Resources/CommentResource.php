<?php

declare(strict_types=1);

namespace Appleton\Threads\Http\Resources;

use Appleton\Threads\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Comment */
class CommentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user,
            'content' => $this->content,
            'reported_at' => $this->reported_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
