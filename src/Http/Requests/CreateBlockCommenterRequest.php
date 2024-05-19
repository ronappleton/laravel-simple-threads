<?php

declare(strict_types=1);

namespace Appleton\Threads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBlockCommenterRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:255'],
            'is_permanent' => ['sometimes', 'required_without:expires_at', 'boolean'],
            'expires_at' => ['sometimes', 'required_without:is_permanent', 'datetime'],
        ];
    }
}
