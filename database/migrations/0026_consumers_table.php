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
        // Create Consumer table (legacy billing system relation table)
        // Links customer + meter + area
        Schema::create('Consumer', function (Blueprint $table) {
            $table->id('c_id');
            $table->unsignedBigInteger('cust_id')->nullable(); // Customer ID
            $table->unsignedBigInteger('cm_id')->nullable();   // Consumer Meter ID
            $table->unsignedInteger('a_id')->nullable();       // Area ID
            $table->unsignedInteger('stat_id')->nullable();
            $table->boolean('chng_mtr_stat')->nullable()->default(false);

            // Foreign key constraints
            $table->foreign('cust_id')
                  ->references('cust_id')
                  ->on('customer')
                  ->onDelete('restrict');

            $table->foreign('cm_id')
                  ->references('cm_id')
                  ->on('consumer_meters')
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
        Schema::dropIfExists('Consumer');
    }
};
