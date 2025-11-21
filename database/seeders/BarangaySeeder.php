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

        $insertCount = 0;
        foreach ($barangays as $barangay) {
            // Check if this barangay already exists
            $existing = DB::table('barangay')
                ->where('b_desc', $barangay)
                ->exists();

            // Only insert if it doesn't exist
            if (!$existing) {
                DB::table('barangay')->insert([
                    'b_desc' => $barangay,
                    'stat_id' => $activeStatusId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $insertCount++;
            }
        }

        $this->command->info('Barangays seeded: ' . $insertCount . ' new barangays in Initao');
    }
}
