<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // All 16 barangays in Initao, Misamis Oriental
        // Format: 'Name' => 'CODE' (4-letter code for account numbers)
        $barangays = [
            'Aluna' => 'ALUN',
            'Andales' => 'ANDA',
            'Apas' => 'APAS',
            'Calacapan' => 'CALA',
            'Gimangpang' => 'GIMA',
            'Jampason' => 'JAMP',
            'Kamelon' => 'KAME',
            'Kanitoan' => 'KANI',
            'Oguis' => 'OGUI',
            'Pagahan' => 'PAGA',
            'Poblacion' => 'POBL',
            'Pontacon' => 'PONT',
            'San Pedro' => 'SANP',
            'Sinalac' => 'SINA',
            'Tawantawan' => 'TAWA',
            'Tubigan' => 'TUBI',
        ];

        $insertCount = 0;
        $updateCount = 0;
        foreach ($barangays as $name => $code) {
            // Check if this barangay already exists
            $existing = DB::table('barangay')
                ->where('b_desc', $name)
                ->first();

            if ($existing) {
                // Update existing record with b_code if missing
                if (empty($existing->b_code)) {
                    DB::table('barangay')
                        ->where('b_id', $existing->b_id)
                        ->update([
                            'b_code' => $code,
                            'updated_at' => now(),
                        ]);
                    $updateCount++;
                }
            } else {
                // Insert new record
                DB::table('barangay')->insert([
                    'b_desc' => $name,
                    'b_code' => $code,
                    'stat_id' => $activeStatusId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $insertCount++;
            }
        }

        $this->command->info("Barangays seeded: {$insertCount} new, {$updateCount} updated with b_code in Initao");
    }
}
