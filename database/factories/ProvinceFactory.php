<?php

namespace Database\Factories;

use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProvinceFactory extends Factory
{
    protected $model = Province::class;

    public function definition(): array
    {
        return [
            'prov_desc' => fake()->state(),
            'stat_id' => 1, // Default to ACTIVE status
        ];
    }
}
