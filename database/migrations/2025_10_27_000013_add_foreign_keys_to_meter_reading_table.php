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
        Schema::table('MeterReading', function (Blueprint $table) {
            // Add foreign key constraints now that all tables exist
            $table->foreign('assignment_id')
                ->references('assignment_id')
                ->on('MeterAssignment')
                ->onDelete('restrict');

            $table->foreign('period_id')
                ->references('per_id')
                ->on('period')
                ->onDelete('restrict');

            $table->foreign('meter_reader_id')
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
        Schema::table('MeterReading', function (Blueprint $table) {
            $foreignKeys = ['assignment_id', 'period_id', 'meter_reader_id'];

            foreach ($foreignKeys as $column) {
                $table->dropForeign(["meter_reading_{$column}_foreign"]);
            }
        });
    }
};
