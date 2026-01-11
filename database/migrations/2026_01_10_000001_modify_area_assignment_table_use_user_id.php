<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Changes meter_reader_id to user_id to reference users table instead of meter_readers table.
     */
    public function up(): void
    {
        Schema::table('AreaAssignment', function (Blueprint $table) {
            // Drop the existing foreign key constraint for meter_reader_id
            $table->dropForeign(['meter_reader_id']);
        });

        Schema::table('AreaAssignment', function (Blueprint $table) {
            // Rename the column from meter_reader_id to user_id
            $table->renameColumn('meter_reader_id', 'user_id');
        });

        Schema::table('AreaAssignment', function (Blueprint $table) {
            // Add the new foreign key constraint to users table
            $table->foreign('user_id')
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
        Schema::table('AreaAssignment', function (Blueprint $table) {
            // Drop the foreign key to users
            $table->dropForeign(['user_id']);
        });

        Schema::table('AreaAssignment', function (Blueprint $table) {
            // Rename back to meter_reader_id
            $table->renameColumn('user_id', 'meter_reader_id');
        });

        Schema::table('AreaAssignment', function (Blueprint $table) {
            // Restore the original foreign key to meter_readers
            $table->foreign('meter_reader_id')
                ->references('mr_id')
                ->on('meter_readers')
                ->onDelete('restrict');
        });
    }
};
