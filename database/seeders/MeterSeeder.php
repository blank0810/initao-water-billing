<?php

namespace Database\Seeders;

use App\Models\Meter;
use App\Models\Status;
use Illuminate\Database\Seeder;

class MeterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $meters = [
            // Neptune meters
            ['mtr_serial' => 'NPT-2024-001', 'mtr_brand' => 'Neptune'],
            ['mtr_serial' => 'NPT-2024-002', 'mtr_brand' => 'Neptune'],
            ['mtr_serial' => 'NPT-2024-003', 'mtr_brand' => 'Neptune'],
            ['mtr_serial' => 'NPT-2024-004', 'mtr_brand' => 'Neptune'],
            ['mtr_serial' => 'NPT-2024-005', 'mtr_brand' => 'Neptune'],

            // Sensus meters
            ['mtr_serial' => 'SNS-2024-001', 'mtr_brand' => 'Sensus'],
            ['mtr_serial' => 'SNS-2024-002', 'mtr_brand' => 'Sensus'],
            ['mtr_serial' => 'SNS-2024-003', 'mtr_brand' => 'Sensus'],
            ['mtr_serial' => 'SNS-2024-004', 'mtr_brand' => 'Sensus'],
            ['mtr_serial' => 'SNS-2024-005', 'mtr_brand' => 'Sensus'],

            // Badger meters
            ['mtr_serial' => 'BDG-2024-001', 'mtr_brand' => 'Badger'],
            ['mtr_serial' => 'BDG-2024-002', 'mtr_brand' => 'Badger'],
            ['mtr_serial' => 'BDG-2024-003', 'mtr_brand' => 'Badger'],
            ['mtr_serial' => 'BDG-2024-004', 'mtr_brand' => 'Badger'],
            ['mtr_serial' => 'BDG-2024-005', 'mtr_brand' => 'Badger'],

            // Itron meters
            ['mtr_serial' => 'ITR-2024-001', 'mtr_brand' => 'Itron'],
            ['mtr_serial' => 'ITR-2024-002', 'mtr_brand' => 'Itron'],
            ['mtr_serial' => 'ITR-2024-003', 'mtr_brand' => 'Itron'],
            ['mtr_serial' => 'ITR-2024-004', 'mtr_brand' => 'Itron'],
            ['mtr_serial' => 'ITR-2024-005', 'mtr_brand' => 'Itron'],

            // Master Meter meters
            ['mtr_serial' => 'MM-2024-001', 'mtr_brand' => 'Master Meter'],
            ['mtr_serial' => 'MM-2024-002', 'mtr_brand' => 'Master Meter'],
            ['mtr_serial' => 'MM-2024-003', 'mtr_brand' => 'Master Meter'],
            ['mtr_serial' => 'MM-2024-004', 'mtr_brand' => 'Master Meter'],
            ['mtr_serial' => 'MM-2024-005', 'mtr_brand' => 'Master Meter'],
        ];

        foreach ($meters as $meter) {
            Meter::firstOrCreate(
                ['mtr_serial' => $meter['mtr_serial']],
                [
                    'mtr_brand' => $meter['mtr_brand'],
                    'stat_id' => $activeStatusId,
                ]
            );
        }

        $this->command->info('Meters seeded: '.count($meters).' meters (5 each of Neptune, Sensus, Badger, Itron, Master Meter)');
    }
}
