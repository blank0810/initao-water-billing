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
        Schema::create('payment', function (Blueprint $table) {
            $table->id('payment_id');
            $table->string('receipt_no')->unique();
            $table->unsignedBigInteger('payer_id'); // Customer ID
            $table->date('payment_date');
            $table->decimal('amount_received', 10, 2);
            $table->unsignedBigInteger('user_id'); // User who recorded the payment
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('payer_id')
                  ->references('cust_id')
                  ->on('customer')
                  ->onDelete('restrict');
                  
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');
                  
            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');
            
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
        // Drop foreign keys first if they exist
        Schema::table('payment', function (Blueprint $table) {
            $foreignKeys = ['payer_id', 'user_id', 'stat_id'];
            
            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('payment', $column)) {
                    $table->dropForeign(["payment_{$column}_foreign"]);
                }
            }
            
            // Drop indexes if they exist
            if (Schema::hasIndex('payment', 'payment_receipt_no_index')) {
                $table->dropIndex('payment_receipt_no_index');
            }
            
            if (Schema::hasIndex('payment', 'payment_date_index')) {
                $table->dropIndex('payment_date_index');
            }
        });
        
        Schema::dropIfExists('payment');
    }
};
