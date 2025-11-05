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
        Schema::create('consumer_meter_relation', function (Blueprint $table) {
            $table->id('c_id');
            $table->unsignedBigInteger('cust_id'); // Customer ID
            $table->unsignedBigInteger('cm_id');   // Consumer Meter ID
            $table->unsignedBigInteger('a_id');    // Area ID
            $table->unsignedBigInteger('stat_id');
            $table->boolean('chng_mtr_stat')->default(false);
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('cust_id')
                  ->references('cust_id')
                  ->on('customer')
                  ->onDelete('restrict');
                  
            $table->foreign('cm_id')
                  ->references('cm_id')
                  ->on('consumer_meter')
                  ->onDelete('restrict');
                  
            $table->foreign('a_id')
                  ->references('a_id')
                  ->on('area')
                  ->onDelete('restrict');
                  
            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');
            
            // Add unique constraint for customer and meter combination
            $table->unique(['cust_id', 'cm_id'], 'consumer_customer_meter_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first if they exist
        Schema::table('consumer_meter_relation', function (Blueprint $table) {
            $foreignKeys = ['cust_id', 'cm_id', 'a_id', 'stat_id'];
            
            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('consumer_meter_relation', $column)) {
                    $table->dropForeign(["consumer_{$column}_foreign"]);
                }
            }
            
            // Drop unique constraint if exists
if (Schema::hasIndex('consumer_meter_relation', 'consumer_customer_meter_unique')) {
                $table->dropUnique('consumer_customer_meter_unique');
            }
        });
        
        Schema::dropIfExists('consumer_meter_relation');
    }
};
