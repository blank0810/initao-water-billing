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
        Schema::table('water_bill_history', function (Blueprint $table) {
            // Add foreign key constraints now that all tables exist
            $table->foreign('connection_id')
                ->references('connection_id')
                ->on('ServiceConnection')
                ->onDelete('cascade');

            $table->foreign('period_id')
                ->references('per_id')
                ->on('period')
                ->onDelete('cascade');

            $table->foreign('prev_reading_id')
                ->references('reading_id')
                ->on('MeterReading')
                ->onDelete('restrict');

            $table->foreign('curr_reading_id')
                ->references('reading_id')
                ->on('MeterReading')
                ->onDelete('restrict');

            $table->foreign('stat_id')
                ->references('stat_id')
                ->on('statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_bill_history', function (Blueprint $table) {
            $foreignKeys = ['connection_id', 'period_id', 'prev_reading_id', 'curr_reading_id', 'stat_id'];

            foreach ($foreignKeys as $column) {
                $table->dropForeign(["water_bill_history_{$column}_foreign"]);
            }
        });
    }
};
