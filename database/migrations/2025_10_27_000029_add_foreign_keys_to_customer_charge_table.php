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
        Schema::table('CustomerCharge', function (Blueprint $table) {
            // Add foreign key constraints now that all tables exist
            $table->foreign('customer_id')
                ->references('cust_id')
                ->on('customer')
                ->onDelete('restrict');

            $table->foreign('application_id')
                ->references('application_id')
                ->on('ServiceApplication')
                ->onDelete('set null');

            $table->foreign('connection_id')
                ->references('connection_id')
                ->on('ServiceConnection')
                ->onDelete('set null');

            $table->foreign('charge_item_id')
                ->references('charge_item_id')
                ->on('ChargeItem')
                ->onDelete('restrict');

            $table->foreign('stat_id')
                ->references('stat_id')
                ->on('statuses')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('CustomerCharge', function (Blueprint $table) {
            $foreignKeys = ['customer_id', 'application_id', 'connection_id', 'charge_item_id', 'stat_id'];

            foreach ($foreignKeys as $column) {
                $table->dropForeign(["customercharge_{$column}_foreign"]);
            }

            // Drop indexes
            $table->dropIndex('customer_charge_customer_index');
            $table->dropIndex('customer_charge_due_date_index');
        });
    }
};
