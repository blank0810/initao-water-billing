<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $insertCount = 0;

        // Create generic puroks from 1-A through 12-B (24 total)
        for ($number = 1; $number <= 12; $number++) {
            foreach (['A', 'B'] as $suffix) {
                $purokDesc = "Purok {$number}-{$suffix}";

                // Check if this purok already exists
                $existing = DB::table('purok')
                    ->where('p_desc', $purokDesc)
                    ->exists();

                // Only insert if it doesn't exist
                if (! $existing) {
                    DB::table('purok')->insert([
                        'p_desc' => $purokDesc,
                        'stat_id' => $activeStatusId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $insertCount++;
                }
            }
        }

        $this->command->info('Puroks seeded: '.$insertCount.' new puroks (Purok 1-A through Purok 12-B)');
    }
}
