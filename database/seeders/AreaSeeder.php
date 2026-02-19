<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates 16 areas matching the barangays of Initao, Misamis Oriental.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $areas = [
            'Aluna',
            'Andales',
            'Apas',
            'Calacapan',
            'Gimangpang',
            'Jampason',
            'Kamelon',
            'Kanitoan',
            'Oguis',
            'Pagahan',
            'Poblacion',
            'Pontacon',
            'San Pedro',
            'Sinalac',
            'Tawantawan',
            'Tubigan',
        ];

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($areas as $area) {
            $existing = DB::table('area')
                ->where('a_desc', $area)
                ->exists();

            if ($existing) {
                $skippedCount++;

                continue;
            }

            DB::table('area')->insert([
                'a_desc' => $area,
                'stat_id' => $activeStatusId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $createdCount++;
        }

        $this->command->info("Areas seeded: {$createdCount} created, {$skippedCount} already existed");
    }
}
