<?php

declare(strict_types=1);

namespace Appleton\Threads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnblockCommenterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'unblock_reason' => ['required'],
        ];
    }
}
