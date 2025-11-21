<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Status;

class BillAdjustmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $adjustmentTypes = [
            [
                'name' => 'Meter Reading Error',
                'direction' => 'credit',
            ],
            [
                'name' => 'Billing Error',
                'direction' => 'credit',
            ],
            [
                'name' => 'Penalty Waiver',
                'direction' => 'credit',
            ],
            [
                'name' => 'Surcharge',
                'direction' => 'debit',
            ],
            [
                'name' => 'Other',
                'direction' => 'debit',
            ],
        ];

        foreach ($adjustmentTypes as $type) {
            // Use updateOrInsert to avoid duplicate entries
            DB::table('BillAdjustmentType')->updateOrInsert(
                ['name' => $type['name']], // Check for existing record by name
                [
                    'name' => $type['name'],
                    'direction' => $type['direction'],
                    'stat_id' => $activeStatusId,
                    'updated_at' => now(),
                ]
            );
        }

        // If this is a new insert, set the created_at timestamp
        DB::table('BillAdjustmentType')
            ->whereNull('created_at')
            ->update(['created_at' => now()]);

        $this->command->info('Bill Adjustment Types seeded: ' . count($adjustmentTypes) . ' adjustment types');
    }
}
