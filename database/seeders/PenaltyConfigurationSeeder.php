<?php

namespace Database\Seeders;

use App\Models\PenaltyConfiguration;
use Illuminate\Database\Seeder;

class PenaltyConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        PenaltyConfiguration::firstOrCreate(
            ['is_active' => true],
            [
                'rate_percentage' => 10.00,
                'effective_date' => now()->toDateString(),
                'created_by' => 1,
            ]
        );
    }
}
