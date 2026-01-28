<?php

namespace Database\Factories;

use App\Models\WaterRate;
use Illuminate\Database\Eloquent\Factories\Factory;

class WaterRateFactory extends Factory
{
    protected $model = WaterRate::class;

    public function definition(): array
    {
        return [
            'period_id' => null, // Default rates
            'class_id' => 1, // Will be overridden in tests
            'range_id' => fake()->numberBetween(1, 5),
            'range_min' => 0,
            'range_max' => 10,
            'rate_val' => fake()->randomFloat(2, 50, 500),
            'rate_inc' => fake()->randomFloat(2, 0, 20),
            'stat_id' => 2, // ACTIVE
        ];
    }
}
