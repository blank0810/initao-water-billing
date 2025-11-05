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
        Schema::create('water_bill', function (Blueprint $table) {
            $table->id('wb_id');
            $table->unsignedBigInteger('cl_id'); // Consumer Ledger ID
            $table->unsignedBigInteger('per_id'); // Period ID
            $table->dateTime('create_date');
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('cl_id')
                  ->references('cl_id')
                  ->on('consumer_ledger')
                  ->onDelete('restrict');
                  
            $table->foreign('per_id')
                  ->references('per_id')
                  ->on('period')
                  ->onDelete('restrict');
                  
            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');
            
            // Add index for search optimization
            $table->index(['cl_id', 'per_id'], 'water_bill_consumer_period_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first if they exist
        Schema::table('water_bill', function (Blueprint $table) {
            $foreignKeys = ['cl_id', 'per_id', 'stat_id'];
            
            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('water_bill', $column)) {
                    $table->dropForeign(["water_bill_{$column}_foreign"]);
                }
            }
            
            // Drop index if exists
            if (Schema::hasIndex('water_bill', 'water_bill_consumer_period_index')) {
                $table->dropIndex('water_bill_consumer_period_index');
            }
        });
        
        Schema::dropIfExists('water_bill');
    }
};
