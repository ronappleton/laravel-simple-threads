<?php

declare(strict_types=1);

namespace Appleton\Threads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateThreadReportRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:255'],
        ];
    }
}
