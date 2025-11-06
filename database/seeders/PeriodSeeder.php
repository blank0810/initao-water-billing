<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Status;

class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Generate periods for the next 12 months starting from current month
        $startDate = now()->startOfMonth();
        $insertCount = 0;

        for ($i = 0; $i < 12; $i++) {
            $endDate = $startDate->copy()->endOfMonth();
            $perCode = $startDate->format('Ym');

            // Check if this period already exists
            $existing = DB::table('period')
                ->where('per_code', $perCode)
                ->exists();

            // Only insert if it doesn't exist
            if (!$existing) {
                DB::table('period')->insert([
                    'per_name' => $startDate->format('F Y'),
                    'per_code' => $perCode,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'is_closed' => false, // New periods start open
                    'closed_at' => null,
                    'closed_by' => null,
                    'stat_id' => $activeStatusId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $insertCount++;
            }

            $startDate->addMonth();
        }

        $this->command->info('Periods seeded: ' . $insertCount . ' new billing periods (12 months)');
    }
}
