<?php

namespace Database\Factories;

use App\Models\Town;
use Illuminate\Database\Eloquent\Factories\Factory;

class TownFactory extends Factory
{
    protected $model = Town::class;

    public function definition(): array
    {
        return [
            't_desc' => fake()->city(),
            'stat_id' => 1, // Default to ACTIVE status
        ];
    }
}
