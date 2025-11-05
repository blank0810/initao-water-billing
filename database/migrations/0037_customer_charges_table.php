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
        Schema::create('CustomerCharge', function (Blueprint $table) {
            $table->id('charge_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('application_id')->nullable();
            $table->unsignedBigInteger('connection_id')->nullable();
            $table->unsignedBigInteger('charge_item_id');
            $table->string('description');
            $table->decimal('quantity', 10, 3);
            $table->decimal('unit_amount', 10, 2);
            $table->decimal('total_amount', 10, 2)->storedAs('quantity * unit_amount');
            $table->date('due_date');
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('customer_id')
                  ->references('cust_id')
                  ->on('customer')
                  ->onDelete('restrict');

            $table->foreign('application_id')
                  ->references('application_id')
                  ->on('ServiceApplication')
                  ->onDelete('set null');

            $table->foreign('connection_id')
                  ->references('connection_id')
                  ->on('service_connection')
                  ->onDelete('set null');

            $table->foreign('charge_item_id')
                  ->references('charge_item_id')
                  ->on('ChargeItem')
                  ->onDelete('restrict');

            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');

            // Add index for search optimization
            $table->index('customer_id', 'customer_charge_customer_index');
            $table->index('due_date', 'customer_charge_due_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CustomerCharge');
    }
};
