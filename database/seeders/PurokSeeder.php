<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Status;

class PurokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Get all barangays
        $barangays = DB::table('barangay')->get();

        $insertCount = 0;

        foreach ($barangays as $barangay) {
            // For each barangay, create puroks from 1-A through 12-B
            for ($number = 1; $number <= 12; $number++) {
                foreach (['A', 'B'] as $suffix) {
                    $purokDesc = "Purok {$number}-{$suffix}";

                    // Check if this purok already exists for this barangay
                    $existing = DB::table('purok')
                        ->where('p_desc', $purokDesc)
                        ->where('b_id', $barangay->b_id)
                        ->exists();

                    // Only insert if it doesn't exist
                    if (!$existing) {
                        DB::table('purok')->insert([
                            'p_desc' => $purokDesc,
                            'b_id' => $barangay->b_id,
                            'stat_id' => $activeStatusId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $insertCount++;
                    }
                }
            }
        }

        $this->command->info('Puroks seeded: ' . $insertCount . ' new puroks (24 per barangay)');
    }
}
