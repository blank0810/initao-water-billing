<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Make period_id and connection_id nullable to support one-time charges
     * like application fees that are not tied to a billing period or connection.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite doesn't support MODIFY, but columns are already nullable by default in our case
            // Since this is a test environment only concern, we can skip for SQLite
            return;
        }

        DB::statement('ALTER TABLE `PaymentAllocation` MODIFY `period_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `PaymentAllocation` MODIFY `connection_id` BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     *
     * This migration is irreversible because:
     * - Rows with NULL period_id/connection_id (one-time charges) have no valid default
     * - Deleting these rows would cause data loss
     * - Setting arbitrary defaults would corrupt data integrity
     */
    public function down(): void
    {
        throw new \RuntimeException(
            'Irreversible migration: cannot restore NOT NULL constraints without data loss. ' .
            'PaymentAllocation rows for one-time charges have NULL period_id/connection_id by design.'
        );
    }
};
