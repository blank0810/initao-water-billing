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
        Schema::create('bill_adjustment', function (Blueprint $table) {
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
                  ->on('water_bill')
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
        // Drop foreign keys first if they exist
        Schema::table('bill_adjustment', function (Blueprint $table) {
            $foreignKeys = ['bill_id', 'bill_adjustment_type_id', 'user_id'];
            
            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('bill_adjustment', $column)) {
                    $table->dropForeign(["billadjustment_{$column}_foreign"]);
                }
            }
            
            // Drop indexes if they exist
            if (Schema::hasIndex('bill_adjustment', 'bill_adjustment_bill_index')) {
                $table->dropIndex('bill_adjustment_bill_index');
            }
            
            if (Schema::hasIndex('bill_adjustment', 'bill_adjustment_created_at_index')) {
                $table->dropIndex('bill_adjustment_created_at_index');
            }
        });
        
        Schema::dropIfExists('bill_adjustment');
    }
};
