<?php

namespace Database\Factories;

use App\Models\Purok;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurokFactory extends Factory
{
    protected $model = Purok::class;

    public function definition(): array
    {
        return [
            'p_desc' => 'Purok '.fake()->numberBetween(1, 20),
            'stat_id' => 1, // Default to ACTIVE status
        ];
    }
}
