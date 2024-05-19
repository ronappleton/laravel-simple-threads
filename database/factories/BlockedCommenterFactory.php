<?php

declare(strict_types=1);

namespace Database\Factories;

use Appleton\Threads\Models\BlockedCommenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlockedCommenterFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = BlockedCommenter::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reason' => $this->faker->sentence(),
        ];
    }
}
