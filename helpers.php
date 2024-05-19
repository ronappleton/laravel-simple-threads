<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\ModelNotFoundException;

if (! function_exists('threads_get_user')) {
    function threads_get_user(string $userId): mixed
    {
        $class = config()->classString('threads.user_model');

        if (! is_callable([$class, 'findOrFail'])) {
            throw new ModelNotFoundException('Commenter User not found');
        }

        return $class::findOrFail($userId);
    }
}
