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
        Schema::create('BillAdjustment', function (Blueprint $table) {
            $table->id('bill_adjustment_id');
            $table->unsignedBigInteger('bill_id'); // Reference to water_bill_history
            $table->unsignedBigInteger('bill_adjustment_type_id');
            $table->decimal('amount', 10, 2);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('user_id'); // User who created the adjustment
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('bill_id')
                  ->references('wb_id')
                  ->on('water_bill_history')
                  ->onDelete('cascade');

            $table->foreign('bill_adjustment_type_id')
                  ->references('bill_adjustment_type_id')
                  ->on('BillAdjustmentType')
                  ->onDelete('restrict');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');

            // Add index for search optimization
            $table->index('bill_id', 'bill_adjustment_bill_index');
            $table->index('created_at', 'bill_adjustment_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('BillAdjustment');
    }
};
