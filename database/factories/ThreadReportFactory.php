<?php

declare(strict_types=1);

namespace Database\Factories;

use Appleton\Threads\Models\ThreadReport;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadReportFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = ThreadReport::class;

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
