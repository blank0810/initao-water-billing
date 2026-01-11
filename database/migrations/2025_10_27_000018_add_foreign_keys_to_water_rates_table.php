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
        Schema::table('water_rates', function (Blueprint $table) {
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
        // Drop foreign key first if it exists
        Schema::table('water_rates', function (Blueprint $table) {
            if (Schema::hasColumn('water_rates', 'stat_id')) {
                $table->dropForeign(['water_rates_stat_id_foreign']);
            }

            // Drop index if exists
            if (Schema::hasIndex('water_rates', 'water_rate_desc_index')) {
                $table->dropIndex('water_rate_desc_index');
            }
        });
    }
};
