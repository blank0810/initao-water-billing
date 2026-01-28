<?php

namespace Database\Factories;

use App\Models\Barangay;
use App\Models\ConsumerAddress;
use App\Models\Province;
use App\Models\Purok;
use App\Models\Status;
use App\Models\Town;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsumerAddressFactory extends Factory
{
    protected $model = ConsumerAddress::class;

    public function definition(): array
    {
        return [
            'p_id' => Purok::factory(),
            'b_id' => Barangay::factory(),
            't_id' => Town::factory(),
            'prov_id' => Province::factory(),
            'stat_id' => 1, // Default to ACTIVE status
        ];
    }
}
