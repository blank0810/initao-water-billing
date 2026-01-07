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
        Schema::table('ServiceConnection', function (Blueprint $table) {
            // Add foreign key constraints now that all tables exist
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
        Schema::dropIfExists('ServiceConnection');
    }
};
