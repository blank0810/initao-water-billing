<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to remove legacy consumer billing tables.
 *
 * These tables were part of the original billing system that has been
 * replaced by the modern ServiceConnection-based system:
 *   - Modern: ServiceConnection → MeterReading → WaterBillHistory → CustomerLedger
 *   - Legacy: Consumer → ConsumerMeter → WaterBill → ConsumerLedger (removed)
 *
 * The legacy tables are no longer used by any Controllers or Services.
 */
return new class extends Migration
{
    /**
     * Legacy tables to be dropped (in order - children first, then parents).
     */
    private array $legacyTables = [
        'meter_reading',          // References consumer_meters, water_bill
        'water_bill_adjustments', // References Consumer
        'misc_bill',              // References consumer_ledger
        'water_bill',             // References consumer_ledger
        'consumer_ledger',        // References Consumer
        'Consumer',               // References consumer_meters
        'consumer_meters',        // Parent table
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks to avoid constraint issues
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach ($this->legacyTables as $table) {
                Schema::dropIfExists($table);
            }
        } finally {
            // Always re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * Reverse the migrations.
     *
     * Note: We do not recreate legacy tables in rollback.
     * If rollback is needed, restore from database backup.
     */
    public function down(): void
    {
        // Legacy tables are intentionally not recreated.
        // The system has migrated to the modern ServiceConnection-based architecture.
        // To restore these tables, use a database backup from before this migration.
    }
};
