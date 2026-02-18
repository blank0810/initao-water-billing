<?php

namespace Database\Seeders;

use App\Models\DocumentSignatory;
use Illuminate\Database\Seeder;

class DocumentSignatorySeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            [
                'position_key' => DocumentSignatory::APPROVING_AUTHORITY,
                'position_title' => 'Approving Authority',
                'sort_order' => 1,
            ],
            [
                'position_key' => DocumentSignatory::MEEDO_OFFICER,
                'position_title' => 'MEEDO Officer',
                'sort_order' => 2,
            ],
        ];

        foreach ($positions as $position) {
            DocumentSignatory::updateOrCreate(
                ['position_key' => $position['position_key']],
                $position
            );
        }

        // Remove deprecated positions
        DocumentSignatory::whereNotIn('position_key', [
            DocumentSignatory::APPROVING_AUTHORITY,
            DocumentSignatory::MEEDO_OFFICER,
        ])->delete();

        $this->command->info('Document signatory positions seeded successfully.');
    }
}
