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
        Schema::create('water_bill_history', function (Blueprint $table) {
            $table->id('bill_id');
            $table->unsignedBigInteger('connection_id');
            $table->unsignedBigInteger('period_id');
            $table->unsignedBigInteger('prev_reading_id');
            $table->unsignedBigInteger('curr_reading_id');
            $table->decimal('consumption', 12, 3);
            $table->decimal('water_amount', 12, 2);
            $table->date('due_date')->nullable();
            $table->decimal('adjustment_total', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->storedAs('water_amount + adjustment_total');
            $table->datetime('created_at', 6)->useCurrent();
            $table->unsignedInteger('stat_id');

            // Foreign keys
            $table->foreign('connection_id')->references('connection_id')->on('ServiceConnection')->onDelete('cascade');
            $table->foreign('period_id')->references('per_id')->on('period')->onDelete('cascade');
            $table->foreign('prev_reading_id')->references('reading_id')->on('MeterReading')->onDelete('restrict');
            $table->foreign('curr_reading_id')->references('reading_id')->on('MeterReading')->onDelete('restrict');
            $table->foreign('stat_id')->references('stat_id')->on('statuses');

            // Indexes
            $table->index('connection_id');
            $table->index('period_id');
            $table->index('stat_id');
            $table->unique(['connection_id', 'period_id'], 'unique_connection_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_bill_history');
    }
};
