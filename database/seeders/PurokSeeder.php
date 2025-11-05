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

        $data = [];
        $purokId = 1;

        foreach ($barangays as $barangay) {
            // For each barangay, create puroks from 1-A through 12-B
            for ($number = 1; $number <= 12; $number++) {
                foreach (['A', 'B'] as $suffix) {
                    $data[] = [
                        'p_id' => $purokId++,
                        'p_desc' => "Purok {$number}-{$suffix}",
                        'b_id' => $barangay->b_id,
                        'stat_id' => $activeStatusId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($data, 100) as $chunk) {
            DB::table('purok')->insert($chunk);
        }

        $this->command->info('Puroks seeded: ' . count($data) . ' puroks (24 per barangay)');
    }
}
