<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add missing indexes for query performance optimization.
     *
     * Foreign key columns already have auto-created indexes from FK constraints.
     * This migration adds composite indexes and indexes on non-FK columns
     * that are frequently used in WHERE, ORDER BY, and JOIN operations.
     */
    public function up(): void
    {
        // =====================================================================
        // HIGH PRIORITY - Critical for billing and meter operations
        // =====================================================================

        // consumer_address: Hierarchical address lookups (e.g. "all addresses in barangay X of town Y")
        Schema::table('consumer_address', function (Blueprint $table) {
            $table->index(['prov_id', 't_id', 'b_id', 'p_id'], 'ca_address_hierarchy_index');
        });

        // MeterAssignment: Date-based queries and "find current meter for connection"
        Schema::table('MeterAssignment', function (Blueprint $table) {
            $table->index('installed_at', 'meter_assignment_installed_at_index');
            $table->index('removed_at', 'meter_assignment_removed_at_index');
            $table->index(['connection_id', 'removed_at'], 'meter_assignment_current_meter_index');
        });

        // ServiceConnection: "Active connections for a customer"
        Schema::table('ServiceConnection', function (Blueprint $table) {
            $table->index(['customer_id', 'stat_id'], 'sc_customer_status_index');
        });

        // MeterReading: Date-based queries and period reports
        Schema::table('MeterReading', function (Blueprint $table) {
            $table->index('reading_date', 'meter_reading_date_index');
            $table->index(['period_id', 'reading_date'], 'meter_reading_period_date_index');
        });

        // water_bill_history: Overdue bill lookups
        Schema::table('water_bill_history', function (Blueprint $table) {
            $table->index('due_date', 'wbh_due_date_index');
            $table->index(['stat_id', 'due_date'], 'wbh_status_due_date_index');
        });

        // PaymentAllocation: "Payments for connection in period" and reverse polymorphic
        Schema::table('PaymentAllocation', function (Blueprint $table) {
            $table->index(['connection_id', 'period_id'], 'pa_connection_period_index');
            $table->index(['target_type', 'target_id'], 'pa_target_polymorphic_index');
        });

        // CustomerLedger: Customer statements and posting timestamps
        Schema::table('CustomerLedger', function (Blueprint $table) {
            $table->index('post_ts', 'cl_post_ts_index');
            $table->index(['customer_id', 'txn_date'], 'cl_customer_txn_date_index');
        });

        // =====================================================================
        // MEDIUM PRIORITY - Reports, batch operations, and schedule management
        // =====================================================================

        // AreaAssignment: "Current assignments for this reader"
        Schema::table('AreaAssignment', function (Blueprint $table) {
            $table->index(['meter_reader_id', 'effective_to'], 'aa_reader_current_index');
        });

        // reading_schedule: End date queries and status+date filtering
        Schema::table('reading_schedule', function (Blueprint $table) {
            $table->index('scheduled_end_date', 'rs_end_date_index');
        });

        // uploaded_readings: Filter by processing status
        Schema::table('uploaded_readings', function (Blueprint $table) {
            $table->index('entry_status', 'ur_entry_status_index');
            $table->index(['schedule_id', 'entry_status'], 'ur_schedule_status_index');
        });

        // reading_schedule_entries: "Pending entries in this schedule"
        Schema::table('reading_schedule_entries', function (Blueprint $table) {
            $table->index('status', 'rse_status_index');
            $table->index(['schedule_id', 'status'], 'rse_schedule_status_index');
        });

        // misc_bill: "Unpaid bills for connection due soon"
        Schema::table('misc_bill', function (Blueprint $table) {
            $table->index(['connection_id', 'is_paid', 'due_date'], 'mb_connection_paid_due_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumer_address', function (Blueprint $table) {
            $table->dropIndex('ca_address_hierarchy_index');
        });

        Schema::table('MeterAssignment', function (Blueprint $table) {
            $table->dropIndex('meter_assignment_installed_at_index');
            $table->dropIndex('meter_assignment_removed_at_index');
            $table->dropIndex('meter_assignment_current_meter_index');
        });

        Schema::table('ServiceConnection', function (Blueprint $table) {
            $table->dropIndex('sc_customer_status_index');
        });

        Schema::table('MeterReading', function (Blueprint $table) {
            $table->dropIndex('meter_reading_date_index');
            $table->dropIndex('meter_reading_period_date_index');
        });

        Schema::table('water_bill_history', function (Blueprint $table) {
            $table->dropIndex('wbh_due_date_index');
            $table->dropIndex('wbh_status_due_date_index');
        });

        Schema::table('PaymentAllocation', function (Blueprint $table) {
            $table->dropIndex('pa_connection_period_index');
            $table->dropIndex('pa_target_polymorphic_index');
        });

        Schema::table('CustomerLedger', function (Blueprint $table) {
            $table->dropIndex('cl_post_ts_index');
            $table->dropIndex('cl_customer_txn_date_index');
        });

        Schema::table('AreaAssignment', function (Blueprint $table) {
            $table->dropIndex('aa_reader_current_index');
        });

        Schema::table('reading_schedule', function (Blueprint $table) {
            $table->dropIndex('rs_end_date_index');
        });

        Schema::table('uploaded_readings', function (Blueprint $table) {
            $table->dropIndex('ur_entry_status_index');
            $table->dropIndex('ur_schedule_status_index');
        });

        Schema::table('reading_schedule_entries', function (Blueprint $table) {
            $table->dropIndex('rse_status_index');
            $table->dropIndex('rse_schedule_status_index');
        });

        Schema::table('misc_bill', function (Blueprint $table) {
            $table->dropIndex('mb_connection_paid_due_index');
        });
    }
};
