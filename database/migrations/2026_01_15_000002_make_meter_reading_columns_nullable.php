<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make period_id and meter_reader_id nullable to support initial meter readings
     * during service connection completion (no billing period or meter reader assigned).
     */
    public function up(): void
    {
        // Drop foreign keys first
        Schema::table('MeterReading', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
            $table->dropForeign(['meter_reader_id']);
        });

        // Make columns nullable
        Schema::table('MeterReading', function (Blueprint $table) {
            $table->unsignedBigInteger('period_id')->nullable()->change();
            $table->unsignedBigInteger('meter_reader_id')->nullable()->change();
        });

        // Re-add foreign keys (now supporting null values)
        Schema::table('MeterReading', function (Blueprint $table) {
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
        // Drop foreign keys
        Schema::table('MeterReading', function (Blueprint $table) {
            $table->dropForeign(['period_id']);
            $table->dropForeign(['meter_reader_id']);
        });

        // Make columns not nullable (will fail if null values exist)
        Schema::table('MeterReading', function (Blueprint $table) {
            $table->unsignedBigInteger('period_id')->nullable(false)->change();
            $table->unsignedBigInteger('meter_reader_id')->nullable(false)->change();
        });

        // Re-add foreign keys
        Schema::table('MeterReading', function (Blueprint $table) {
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
};
