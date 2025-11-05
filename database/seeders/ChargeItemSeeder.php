<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Status;

class ChargeItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $chargeItems = [
            // One-time Application Fees
            [
                'name' => 'Connection Fee',
                'code' => 'CONN_FEE',
                'description' => 'Initial connection fee for new water service',
                'default_amount' => 500.00,
                'charge_type' => 'one_time',
                'is_taxable' => false,
            ],
            [
                'name' => 'Service Deposit',
                'code' => 'SERVICE_DEP',
                'description' => 'Refundable security deposit',
                'default_amount' => 300.00,
                'charge_type' => 'one_time',
                'is_taxable' => false,
            ],
            [
                'name' => 'Meter Deposit',
                'code' => 'METER_DEP',
                'description' => 'Water meter deposit',
                'default_amount' => 200.00,
                'charge_type' => 'one_time',
                'is_taxable' => false,
            ],
            [
                'name' => 'Application Processing Fee',
                'code' => 'APP_PROC',
                'description' => 'Non-refundable application processing fee',
                'default_amount' => 50.00,
                'charge_type' => 'one_time',
                'is_taxable' => false,
            ],
            [
                'name' => 'Installation Fee',
                'code' => 'INSTALL_FEE',
                'description' => 'Meter installation fee',
                'default_amount' => 800.00,
                'charge_type' => 'one_time',
                'is_taxable' => false,
            ],

            // Recurring Charges
            [
                'name' => 'Monthly Service Charge',
                'code' => 'MONTHLY_SVC',
                'description' => 'Monthly service maintenance charge',
                'default_amount' => 50.00,
                'charge_type' => 'recurring',
                'is_taxable' => false,
            ],

            // One-time Penalty/Miscellaneous Charges
            [
                'name' => 'Reconnection Fee',
                'code' => 'RECONN_FEE',
                'description' => 'Fee for reconnecting disconnected service',
                'default_amount' => 300.00,
                'charge_type' => 'one_time',
                'is_taxable' => false,
            ],
            [
                'name' => 'Late Payment Penalty',
                'code' => 'LATE_PENALTY',
                'description' => 'Penalty for late payment',
                'default_amount' => 50.00,
                'charge_type' => 'one_time',
                'is_taxable' => false,
            ],
            [
                'name' => 'Meter Transfer Fee',
                'code' => 'METER_TRANSFER',
                'description' => 'Fee for transferring meter to new location',
                'default_amount' => 250.00,
                'charge_type' => 'one_time',
                'is_taxable' => false,
            ],
            [
                'name' => 'Meter Replacement Fee',
                'code' => 'METER_REPLACE',
                'description' => 'Fee for replacing damaged meter',
                'default_amount' => 500.00,
                'charge_type' => 'one_time',
                'is_taxable' => false,
            ],
        ];

        $data = [];
        foreach ($chargeItems as $index => $item) {
            $data[] = [
                'charge_item_id' => $index + 1,
                'name' => $item['name'],
                'code' => $item['code'],
                'description' => $item['description'],
                'default_amount' => $item['default_amount'],
                'charge_type' => $item['charge_type'],
                'is_taxable' => $item['is_taxable'],
                'stat_id' => $activeStatusId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('ChargeItem')->insert($data);

        $this->command->info('Charge Items seeded: ' . count($chargeItems) . ' charge items');
    }
}
