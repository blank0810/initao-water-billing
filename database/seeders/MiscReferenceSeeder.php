<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Status;
use App\Models\User;

class MiscReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Get the first user (admin) as the creator
        // If no users exist, skip seeding (user should be seeded first)
        if (!DB::table('users')->exists()) {
            $this->command->warn('No users found. Please seed users first before seeding misc references.');
            return;
        }

        $adminId = DB::table('users')->first()->id;

        $miscReferences = [
            [
                'reference_type' => 'penalty',
                'reference_code' => 'LATE_PAYMENT',
                'description' => 'Late payment penalty',
                'default_amount' => 100.00,
            ],
            [
                'reference_type' => 'discount',
                'reference_code' => 'SENIOR_CITIZEN',
                'description' => 'Senior citizen discount',
                'default_amount' => 100.00,
            ],
            [
                'reference_type' => 'surcharge',
                'reference_code' => 'DAMAGED_METER',
                'description' => 'Damaged meter surcharge',
                'default_amount' => 500.00,
            ],
        ];

        $insertCount = 0;
        foreach ($miscReferences as $reference) {
            // Check if this reference code already exists
            $existing = DB::table('misc_reference')
                ->where('reference_code', $reference['reference_code'])
                ->exists();

            // Only insert if it doesn't exist
            if (!$existing) {
                DB::table('misc_reference')->insert([
                    'reference_type' => $reference['reference_type'],
                    'reference_code' => $reference['reference_code'],
                    'description' => $reference['description'],
                    'default_amount' => $reference['default_amount'],
                    'is_active' => true,
                    'stat_id' => $activeStatusId,
                    'created_by' => $adminId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $insertCount++;
            }
        }

        $this->command->info('Misc References seeded: ' . $insertCount . ' new reference types');
    }
}
