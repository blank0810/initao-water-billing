<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Status;
use Illuminate\Database\Seeder;

class PerformanceTestCustomerSeeder extends Seeder
{
    /**
     * Seed customers for performance testing.
     *
     * Creates 150 customers to test how the customer list handles large datasets.
     * Used for integration testing to verify performance benchmarks are met.
     */
    public function run(): void
    {
        $this->command->info('Creating 150 customers for performance testing...');

        // Ensure statuses exist
        if (! Status::where('stat_desc', Status::ACTIVE)->exists()) {
            Status::create(['stat_id' => 1, 'stat_desc' => Status::ACTIVE]);
        }
        if (! Status::where('stat_desc', Status::INACTIVE)->exists()) {
            Status::create(['stat_id' => 2, 'stat_desc' => Status::INACTIVE]);
        }
        if (! Status::where('stat_desc', Status::PENDING)->exists()) {
            Status::create(['stat_id' => 3, 'stat_desc' => Status::PENDING]);
        }

        $activeId = Status::getIdByDescription(Status::ACTIVE);
        $inactiveId = Status::getIdByDescription(Status::INACTIVE);
        $pendingId = Status::getIdByDescription(Status::PENDING);

        // Create customers with varied statuses (realistic distribution)
        // 70% Active, 20% Inactive, 10% Pending
        Customer::factory()->count(105)->create(['stat_id' => $activeId]);
        Customer::factory()->count(30)->create(['stat_id' => $inactiveId]);
        Customer::factory()->count(15)->create(['stat_id' => $pendingId]);

        $this->command->info('Successfully created 150 customers for performance testing');
        $this->command->info('  - 105 ACTIVE customers');
        $this->command->info('  - 30 INACTIVE customers');
        $this->command->info('  - 15 PENDING customers');
    }
}
