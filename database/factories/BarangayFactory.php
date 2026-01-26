<?php

namespace Database\Factories;

use App\Models\Barangay;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangayFactory extends Factory
{
    protected $model = Barangay::class;

    public function definition(): array
    {
        return [
            'b_desc' => fake()->unique()->city(),
            'b_code' => strtoupper(fake()->lexify('???-###')),
            'stat_id' => 1, // Default to ACTIVE status
        ];
    }
}
