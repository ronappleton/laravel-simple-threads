<?php

declare(strict_types=1);

namespace Database\Factories;

use Appleton\Threads\Models\Thread;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Thread::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
        ];
    }
}
