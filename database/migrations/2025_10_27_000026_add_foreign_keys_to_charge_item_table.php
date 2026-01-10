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
        Schema::table('ChargeItem', function (Blueprint $table) {
            // Add foreign key constraint now that all tables exist
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
        Schema::table('ChargeItem', function (Blueprint $table) {
            $table->dropForeign(['stat_id']);

            // Drop indexes if they exist
            if (Schema::hasIndex('ChargeItem', 'charge_item_code_index')) {
                $table->dropIndex('charge_item_code_index');
            }

            if (Schema::hasIndex('ChargeItem', 'charge_item_type_index')) {
                $table->dropIndex('charge_item_type_index');
            }
        });
    }
};
