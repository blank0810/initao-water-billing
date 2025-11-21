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
        Schema::create('PaymentAllocation', function (Blueprint $table) {
            $table->id('payment_allocation_id');
            $table->unsignedBigInteger('payment_id');
            $table->string('target_type'); // e.g., 'water_bill', 'penalty', etc.
            $table->unsignedBigInteger('target_id'); // Polymorphic relation ID
            $table->decimal('amount_applied', 10, 2);
            $table->unsignedBigInteger('period_id');
            $table->unsignedBigInteger('connection_id'); // Service connection
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index(['payment_id', 'target_type', 'target_id'], 'payment_allocation_index');
            $table->index('period_id', 'payment_allocation_period_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PaymentAllocation');
    }
};
