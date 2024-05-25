<?php

declare(strict_types=1);

namespace Appleton\Threads\Notifications\Concerns;

use Illuminate\Support\Str;

trait HasClass
{
    /**
     * @param class-string $class
     */
    public function getSnakeName(string $class): string
    {
        $classParts = explode('\\', $class);

        return Str::snake(end($classParts));
    }
}