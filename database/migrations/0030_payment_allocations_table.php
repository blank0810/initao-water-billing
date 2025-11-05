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
        Schema::create('payment_allocation', function (Blueprint $table) {
            $table->id('payment_allocation_id');
            $table->unsignedBigInteger('payment_id');
            $table->string('target_type'); // e.g., 'water_bill', 'penalty', etc.
            $table->unsignedBigInteger('target_id'); // Polymorphic relation ID
            $table->decimal('amount_applied', 10, 2);
            $table->unsignedBigInteger('period_id');
            $table->unsignedBigInteger('connection_id'); // Service connection
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('payment_id')
                  ->references('payment_id')
                  ->on('payment')
                  ->onDelete('cascade');
                  
            $table->foreign('period_id')
                  ->references('per_id')
                  ->on('period')
                  ->onDelete('restrict');
                  
            $table->foreign('connection_id')
                  ->references('connection_id')
                  ->on('service_connection')
                  ->onDelete('restrict');
            
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
        // Drop foreign keys first if they exist
        Schema::table('payment_allocation', function (Blueprint $table) {
            $foreignKeys = ['payment_id', 'period_id', 'connection_id'];
            
            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('payment_allocation', $column)) {
                    $table->dropForeign(["paymentallocation_{$column}_foreign"]);
                }
            }
            
            // Drop indexes if they exist
            if (Schema::hasIndex('payment_allocation', 'payment_allocation_index')) {
                $table->dropIndex('payment_allocation_index');
            }
            
            if (Schema::hasIndex('payment_allocation', 'payment_allocation_period_index')) {
                $table->dropIndex('payment_allocation_period_index');
            }
        });
        
        Schema::dropIfExists('payment_allocation');
    }
};
