<?php

declare(strict_types=1);

namespace Appleton\Threads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'content' => ['sometimes', 'string'],
            'reported_at' => ['sometimes', 'date'],
            'hidden_at' => ['sometimes', 'date'],
        ];
    }
}
