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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('bill_id')->nullable(); // Can be null for advance payments
            $table->string('reference_number')->nullable(); // For tracking external references
            $table->string('transaction_type'); // e.g., 'PAYMENT', 'ADJUSTMENT', 'REFUND'
            $table->decimal('amount', 12, 2);

            // For tracking what the payment is applied to
            $table->string('applied_to_type')->nullable(); // e.g., 'WATER_BILL', 'MISC_BILL', 'PENALTY'
            $table->unsignedBigInteger('applied_to_id')->nullable(); // Polymorphic relation ID

            $table->text('notes')->nullable();
            $table->unsignedBigInteger('processed_by');
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index('payment_id', 'payment_transaction_payment_index');
            $table->index('bill_id', 'payment_transaction_bill_index');
            $table->index('reference_number', 'payment_transaction_reference_index');
            $table->index(['applied_to_type', 'applied_to_id'], 'payment_transaction_applied_to_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
