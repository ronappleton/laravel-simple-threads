<?php

declare(strict_types=1);

namespace Appleton\Threads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateThreadRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string'],
            'content' => ['sometimes', 'string'],
            'locked_at' => ['sometimes', 'date'],
            'pinned_at' => ['sometimes', 'date'],
            'hidden_at' => ['sometimes', 'date'],
            'reported_at' => ['sometimes', 'date'],
        ];
    }
}
