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

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index(['cl_id', 'per_id'], 'water_bill_consumer_period_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop index if exists
        Schema::table('water_bill', function (Blueprint $table) {
            if (Schema::hasIndex('water_bill', 'water_bill_consumer_period_index')) {
                $table->dropIndex('water_bill_consumer_period_index');
            }
        });

        Schema::dropIfExists('water_bill');
    }
};
