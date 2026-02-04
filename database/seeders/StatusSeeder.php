<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            // Base statuses
            Status::PENDING,
            Status::ACTIVE,
            Status::INACTIVE,

            // Application Workflow
            Status::VERIFIED,
            Status::PAID,
            Status::SCHEDULED,
            Status::CONNECTED,
            Status::REJECTED,
            Status::CANCELLED,

            // Connection Lifecycle
            Status::SUSPENDED,
            Status::DISCONNECTED,

            // Reading Schedule
            Status::COMPLETED,

            // Billing
            Status::OVERDUE,
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate(
                ['stat_desc' => $status],
                ['stat_desc' => $status]
            );
        }

        $this->command->info('✅ Status seeder completed - '.count($statuses).' statuses ensured');
    }
}
