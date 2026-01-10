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
        Schema::create('Payment', function (Blueprint $table) {
            $table->id('payment_id');
            $table->string('receipt_no')->unique();
            $table->unsignedBigInteger('payer_id'); // Customer ID
            $table->date('payment_date');
            $table->decimal('amount_received', 10, 2);
            $table->unsignedBigInteger('user_id'); // User who recorded the payment
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index('receipt_no', 'payment_receipt_no_index');
            $table->index('payment_date', 'payment_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Payment');
    }
};
