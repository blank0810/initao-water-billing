<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds only the current month period. New periods should be created
     * via the Period Management UI, which automatically copies rates
     * from the most recent previous period.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Create only the current month period
        $startDate = now()->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $perCode = $startDate->format('Ym');

        // Check if this period already exists
        $existing = DB::table('period')
            ->where('per_code', $perCode)
            ->exists();

        if (! $existing) {
            DB::table('period')->insert([
                'per_name' => $startDate->format('F Y'),
                'per_code' => $perCode,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'is_closed' => false,
                'closed_at' => null,
                'closed_by' => null,
                'stat_id' => $activeStatusId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info("Period seeded: {$startDate->format('F Y')}");
        } else {
            $this->command->info("Period already exists: {$startDate->format('F Y')}");
        }
    }
}
