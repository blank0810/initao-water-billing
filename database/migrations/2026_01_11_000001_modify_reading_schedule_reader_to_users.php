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
        Schema::table('reading_schedule', function (Blueprint $table) {
            // Drop the existing foreign key constraint to meter_readers
            $table->dropForeign(['reader_id']);
        });

        Schema::table('reading_schedule', function (Blueprint $table) {
            // Add new foreign key constraint to users table
            $table->foreign('reader_id')
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
        Schema::table('reading_schedule', function (Blueprint $table) {
            // Drop the users foreign key
            $table->dropForeign(['reader_id']);
        });

        Schema::table('reading_schedule', function (Blueprint $table) {
            // Restore the original meter_readers foreign key
            $table->foreign('reader_id')
                ->references('mr_id')
                ->on('meter_readers')
                ->onDelete('restrict');
        });
    }
};
