<?php

namespace Database\Factories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

class AreaFactory extends Factory
{
    protected $model = Area::class;

    public function definition(): array
    {
        return [
            'a_desc' => fake()->unique()->city() . ' Area',
            'stat_id' => 1, // Default to ACTIVE status
        ];
    }
}
