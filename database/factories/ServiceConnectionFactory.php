<?php

namespace Database\Factories;

use App\Models\ServiceConnection;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceConnectionFactory extends Factory
{
    protected $model = ServiceConnection::class;

    public function definition(): array
    {
        return [
            'account_no' => 'SC-' . fake()->unique()->numerify('######'),
            'customer_id' => 1, // Will be overridden in tests
            'address_id' => 1, // Will be overridden in tests
            'account_type_id' => 1,
            'area_id' => 1, // Will be overridden in tests
            'started_at' => now(),
            'ended_at' => null,
            'stat_id' => 1, // ACTIVE
        ];
    }
}
