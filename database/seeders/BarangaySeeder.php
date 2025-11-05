<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Status;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // All 16 barangays in Initao, Misamis Oriental
        $barangays = [
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

        $data = [];
        foreach ($barangays as $index => $barangay) {
            $data[] = [
                'b_id' => $index + 1,
                'b_desc' => $barangay,
                'stat_id' => $activeStatusId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('barangay')->insert($data);

        $this->command->info('Barangays seeded: ' . count($barangays) . ' barangays in Initao');
    }
}
