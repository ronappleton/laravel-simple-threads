<?php

declare(strict_types=1);

namespace Appleton\Threads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateThreadRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'threaded_id' => ['required', 'string'],
            'threaded_type' => ['required', 'string'],
            'user_id' => ['required', 'string'],
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
        ];
    }
}
