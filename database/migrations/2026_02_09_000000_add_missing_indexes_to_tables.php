<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add missing indexes for query performance optimization.
     *
     * Foreign key columns already have auto-created indexes from FK constraints.
     * This migration adds composite indexes and indexes on non-FK columns
     * that are frequently used in WHERE, ORDER BY, and JOIN operations.
     *
     * Uses addIndexSafely() to handle partial runs gracefully.
     */
    public function up(): void
    {
        // =====================================================================
        // HIGH PRIORITY - Critical for billing and meter operations
        // =====================================================================

        // consumer_address: Hierarchical address lookups (e.g. "all addresses in barangay X of town Y")
        $this->addIndexSafely('consumer_address', ['prov_id', 't_id', 'b_id', 'p_id'], 'ca_address_hierarchy_index');

        // MeterAssignment: Date-based queries and "find current meter for connection"
        $this->addIndexSafely('MeterAssignment', ['installed_at'], 'meter_assignment_installed_at_index');
        $this->addIndexSafely('MeterAssignment', ['removed_at'], 'meter_assignment_removed_at_index');
        $this->addIndexSafely('MeterAssignment', ['connection_id', 'removed_at'], 'meter_assignment_current_meter_index');

        // ServiceConnection: "Active connections for a customer"
        $this->addIndexSafely('ServiceConnection', ['customer_id', 'stat_id'], 'sc_customer_status_index');

        // MeterReading: Date-based queries and period reports
        $this->addIndexSafely('MeterReading', ['reading_date'], 'meter_reading_date_index');
        $this->addIndexSafely('MeterReading', ['period_id', 'reading_date'], 'meter_reading_period_date_index');

        // water_bill_history: Overdue bill lookups
        $this->addIndexSafely('water_bill_history', ['due_date'], 'wbh_due_date_index');
        $this->addIndexSafely('water_bill_history', ['stat_id', 'due_date'], 'wbh_status_due_date_index');

        // PaymentAllocation: "Payments for connection in period" and reverse polymorphic
        $this->addIndexSafely('PaymentAllocation', ['connection_id', 'period_id'], 'pa_connection_period_index');
        $this->addIndexSafely('PaymentAllocation', ['target_type', 'target_id'], 'pa_target_polymorphic_index');

        // CustomerLedger: Customer statements and posting timestamps
        $this->addIndexSafely('CustomerLedger', ['post_ts'], 'cl_post_ts_index');
        $this->addIndexSafely('CustomerLedger', ['customer_id', 'txn_date'], 'cl_customer_txn_date_index');

        // =====================================================================
        // MEDIUM PRIORITY - Reports, batch operations, and schedule management
        // =====================================================================

        // AreaAssignment: "Current assignments for this reader"
        $this->addIndexSafely('AreaAssignment', ['user_id', 'effective_to'], 'aa_reader_current_index');

        // reading_schedule: End date queries and status+date filtering
        $this->addIndexSafely('reading_schedule', ['scheduled_end_date'], 'rs_end_date_index');

        // uploaded_readings: Filter by processing status
        $this->addIndexSafely('uploaded_readings', ['entry_status'], 'ur_entry_status_index');
        $this->addIndexSafely('uploaded_readings', ['schedule_id', 'entry_status'], 'ur_schedule_status_index');

        // reading_schedule_entries: "Pending entries in this schedule"
        $this->addIndexSafely('reading_schedule_entries', ['status'], 'rse_status_index');
        $this->addIndexSafely('reading_schedule_entries', ['schedule_id', 'status'], 'rse_schedule_status_index');

        // misc_bill: "Unpaid bills for connection due soon"
        $this->addIndexSafely('misc_bill', ['connection_id', 'is_paid', 'due_date'], 'mb_connection_paid_due_index');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexSafely('consumer_address', 'ca_address_hierarchy_index');
        $this->dropIndexSafely('MeterAssignment', 'meter_assignment_installed_at_index');
        $this->dropIndexSafely('MeterAssignment', 'meter_assignment_removed_at_index');
        $this->dropIndexSafely('MeterAssignment', 'meter_assignment_current_meter_index');
        $this->dropIndexSafely('ServiceConnection', 'sc_customer_status_index');
        $this->dropIndexSafely('MeterReading', 'meter_reading_date_index');
        $this->dropIndexSafely('MeterReading', 'meter_reading_period_date_index');
        $this->dropIndexSafely('water_bill_history', 'wbh_due_date_index');
        $this->dropIndexSafely('water_bill_history', 'wbh_status_due_date_index');
        $this->dropIndexSafely('PaymentAllocation', 'pa_connection_period_index');
        $this->dropIndexSafely('PaymentAllocation', 'pa_target_polymorphic_index');
        $this->dropIndexSafely('CustomerLedger', 'cl_post_ts_index');
        $this->dropIndexSafely('CustomerLedger', 'cl_customer_txn_date_index');
        $this->dropIndexSafely('AreaAssignment', 'aa_reader_current_index');
        $this->dropIndexSafely('reading_schedule', 'rs_end_date_index');
        $this->dropIndexSafely('uploaded_readings', 'ur_entry_status_index');
        $this->dropIndexSafely('uploaded_readings', 'ur_schedule_status_index');
        $this->dropIndexSafely('reading_schedule_entries', 'rse_status_index');
        $this->dropIndexSafely('reading_schedule_entries', 'rse_schedule_status_index');
        $this->dropIndexSafely('misc_bill', 'mb_connection_paid_due_index');
    }

    /**
     * Add an index only if it doesn't already exist.
     */
    private function addIndexSafely(string $table, array $columns, string $indexName): void
    {
        if (! $this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName) {
                $blueprint->index($columns, $indexName);
            });
        }
    }

    /**
     * Drop an index only if it exists.
     */
    private function dropIndexSafely(string $table, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
                $blueprint->dropIndex($indexName);
            });
        }
    }

    /**
     * Check if an index exists on a table.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }
};
