<?php

namespace Database\Factories;

use App\Models\MeterAssignment;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeterAssignmentFactory extends Factory
{
    protected $model = MeterAssignment::class;

    public function definition(): array
    {
        return [
            'connection_id' => 1,
            'meter_id' => 1,
            'installed_at' => now(),
            'removed_at' => null,
            'install_read' => 0.000,
            'removal_read' => null,
        ];
    }
}
