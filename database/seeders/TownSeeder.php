<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Status;

class TownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Check if Initao town already exists
        $existing = DB::table('town')
            ->where('t_desc', 'Initao')
            ->exists();

        // Only insert if it doesn't exist
        if (!$existing) {
            DB::table('town')->insert([
                't_desc' => 'Initao',
                'prov_id' => 1, // Misamis Oriental
                'stat_id' => $activeStatusId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('Town seeded: Initao');
        } else {
            $this->command->info('Town already exists: Initao');
        }
    }
}
