<?php

namespace Database\Factories;

use App\Models\Meter;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeterFactory extends Factory
{
    protected $model = Meter::class;

    public function definition(): array
    {
        return [
            'mtr_serial' => 'MTR-'.fake()->unique()->numerify('####-###'),
            'mtr_brand' => fake()->randomElement(['Neptune', 'Sensus', 'Badger', 'Itron']),
            'stat_id' => 1,
        ];
    }
}
