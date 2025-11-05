<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Status;

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

        $data = [];
        foreach ($accountTypes as $index => $type) {
            $data[] = [
                'at_id' => $index + 1,
                'at_desc' => $type,
                'stat_id' => $activeStatusId,
            ];
        }

        DB::table('account_type')->insert($data);

        $this->command->info('Account Types seeded: ' . count($accountTypes) . ' types');
    }
}
