<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // FULLTEXT index on customer name columns for natural language search
        DB::statement('ALTER TABLE customer ADD FULLTEXT INDEX customer_fulltext_name (cust_first_name, cust_last_name)');

        // Regular index on resolution_no for prefix LIKE search
        Schema::table('customer', function (Blueprint $table) {
            $table->index('resolution_no', 'customer_resolution_no_index');
        });

        // Index on MeterAssignment join columns for faster joins
        Schema::table('MeterAssignment', function (Blueprint $table) {
            $table->index('connection_id', 'meter_assignment_connection_id_index');
            $table->index('meter_id', 'meter_assignment_meter_id_index');
        });
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE customer DROP INDEX customer_fulltext_name');

        Schema::table('customer', function (Blueprint $table) {
            $table->dropIndex('customer_resolution_no_index');
        });

        Schema::table('MeterAssignment', function (Blueprint $table) {
            $table->dropIndex('meter_assignment_connection_id_index');
            $table->dropIndex('meter_assignment_meter_id_index');
        });
    }
};
