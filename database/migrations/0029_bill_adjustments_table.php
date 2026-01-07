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
        Schema::create('BillAdjustment', function (Blueprint $table) {
            $table->id('bill_adjustment_id');
            $table->unsignedBigInteger('bill_id'); // Reference to water_bill_history
            $table->unsignedBigInteger('bill_adjustment_type_id');
            $table->decimal('amount', 10, 2);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('user_id'); // User who created the adjustment
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

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
