<?php

declare(strict_types=1);

namespace Database\Factories;

use Appleton\Threads\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Comment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->sentence(),
        ];
    }
}
