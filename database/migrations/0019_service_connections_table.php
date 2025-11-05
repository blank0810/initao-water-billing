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
        Schema::create('service_connection', function (Blueprint $table) {
            $table->id('connection_id');
            $table->string('account_no')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('address_id');
            $table->unsignedBigInteger('account_type_id');
            $table->unsignedBigInteger('rate_id');
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('customer_id')
                  ->references('cust_id')
                  ->on('customer')
                  ->onDelete('restrict');
                  
            $table->foreign('address_id')
                  ->references('ca_id')
                  ->on('consumer_address')
                  ->onDelete('restrict');
                  
            $table->foreign('account_type_id')
                  ->references('at_id')
                  ->on('account_type')
                  ->onDelete('restrict');
                  
            $table->foreign('rate_id')
                  ->references('wr_id')
                  ->on('water_rates')
                  ->onDelete('restrict');
                  
            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first if they exist
        Schema::table('service_connection', function (Blueprint $table) {
            $foreignKeys = [
                'customer_id',
                'address_id',
                'account_type_id',
                'rate_id',
                'stat_id'
            ];
            
            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('service_connection', $column)) {
                    $table->dropForeign([$column]);
                }
            }
        });
        
        Schema::dropIfExists('service_connection');
    }
};
