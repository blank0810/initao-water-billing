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
        Schema::table('PaymentAllocation', function (Blueprint $table) {
            // Add foreign key constraints now that all tables exist
            $table->foreign('payment_id')
                ->references('payment_id')
                ->on('Payment')
                ->onDelete('cascade');

            $table->foreign('period_id')
                ->references('per_id')
                ->on('period')
                ->onDelete('restrict');

            $table->foreign('connection_id')
                ->references('connection_id')
                ->on('ServiceConnection')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('PaymentAllocation', function (Blueprint $table) {
            $foreignKeys = ['payment_id', 'period_id', 'connection_id'];

            foreach ($foreignKeys as $column) {
                $table->dropForeign(["paymentallocation_{$column}_foreign"]);
            }

            // Drop indexes
            $table->dropIndex('payment_allocation_index');
            $table->dropIndex('payment_allocation_period_index');
        });
    }
};
