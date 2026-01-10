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
        Schema::table('meter', function (Blueprint $table) {
            // Add foreign key constraints now that all tables exist
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
        Schema::table('meter', function (Blueprint $table) {
            if (Schema::hasColumn('meter', 'stat_id')) {
                $table->dropForeign(['stat_id']);
            }
        });
    }
};
