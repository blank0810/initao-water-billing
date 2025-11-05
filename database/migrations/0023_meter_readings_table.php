<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('MeterReading', function (Blueprint $table) {
            $table->id('reading_id');
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedBigInteger('period_id');
            $table->date('reading_date');
            $table->decimal('reading_value', 10, 3);
            $table->boolean('is_estimated')->default(false);
            $table->unsignedBigInteger('meter_reader_id');
            $table->timestamps();

            // Foreign key constraints
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

            // Add composite index for search optimization
            $table->index(['assignment_id', 'period_id'], 'meter_reading_assignment_period_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('MeterReading');
    }
};
