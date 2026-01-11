<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The new workflow statuses to add.
     */
    private array $newStatuses = [
        // Application Workflow
        ['stat_desc' => 'VERIFIED'],
        ['stat_desc' => 'PAID'],
        ['stat_desc' => 'SCHEDULED'],
        ['stat_desc' => 'CONNECTED'],
        ['stat_desc' => 'REJECTED'],
        ['stat_desc' => 'CANCELLED'],
        // Connection Lifecycle
        ['stat_desc' => 'SUSPENDED'],
        ['stat_desc' => 'DISCONNECTED'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->newStatuses as $status) {
            // Only insert if status doesn't already exist
            if (! DB::table('statuses')->where('stat_desc', $status['stat_desc'])->exists()) {
                DB::table('statuses')->insert($status);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $statusDescriptions = array_column($this->newStatuses, 'stat_desc');
        DB::table('statuses')->whereIn('stat_desc', $statusDescriptions)->delete();
    }
};
