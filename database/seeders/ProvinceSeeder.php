<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Status;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        DB::table('province')->insert([
            'prov_id' => 1,
            'prov_desc' => 'Misamis Oriental',
            'stat_id' => $activeStatusId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Province seeded: Misamis Oriental');
    }
}
