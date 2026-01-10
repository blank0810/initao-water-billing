<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('misc_bill', function (Blueprint $table) {
            // Add foreign key constraints now that all tables exist
            $table->foreign('connection_id')
                ->references('connection_id')
                ->on('ServiceConnection')
                ->onDelete('restrict');

            $table->foreign('misc_reference_id')
                ->references('misc_reference_id')
                ->on('misc_reference')
                ->onDelete('restrict');

            $table->foreign('stat_id')
                ->references('stat_id')
                ->on('statuses')
                ->onDelete('restrict');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first if they exist
        Schema::table('misc_bill', function (Blueprint $table) {
            $foreignKeys = [
                'connection_id',
                'misc_reference_id',
                'stat_id',
                'created_by',
            ];

            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('misc_bill', $column)) {
                    $table->dropForeign(["miscbill_{$column}_foreign"]);
                }
            }

            // Drop indexes if they exist
            $indexes = [
                'misc_bill_connection_index',
                'misc_bill_number_index',
                'misc_bill_billing_date_index',
                'misc_bill_paid_index',
            ];

            foreach ($indexes as $index) {
                if (Schema::hasIndex('misc_bill', $index)) {
                    $table->dropIndex($index);
                }
            }
        });
    }
};
