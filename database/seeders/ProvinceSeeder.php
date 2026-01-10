<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Check if Misamis Oriental province already exists
        $existing = DB::table('province')
            ->where('prov_desc', 'Misamis Oriental')
            ->exists();

        // Only insert if it doesn't exist
        if (! $existing) {
            DB::table('province')->insert([
                'prov_desc' => 'Misamis Oriental',
                'stat_id' => $activeStatusId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('Province seeded: Misamis Oriental');
        } else {
            $this->command->info('Province already exists: Misamis Oriental');
        }
    }
}
