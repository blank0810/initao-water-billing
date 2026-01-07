<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $accountTypes = [
            'Individual',
            'Corporation',
            'Partnership',
            'Government',
            'Non-Profit Organization',
            'Cooperative',
        ];

        foreach ($accountTypes as $type) {
            // Use updateOrInsert to avoid duplicate entries
            DB::table('account_type')->updateOrInsert(
                ['at_desc' => $type], // Check for existing record by description
                [
                    'at_desc' => $type,
                    'stat_id' => $activeStatusId,
                    'updated_at' => now(),
                ]
            );
        }

        // If this is a new insert, set the created_at timestamp
        DB::table('account_type')
            ->whereNull('created_at')
            ->update(['created_at' => now()]);

        $this->command->info('Account Types seeded: '.count($accountTypes).' types');
    }
}
