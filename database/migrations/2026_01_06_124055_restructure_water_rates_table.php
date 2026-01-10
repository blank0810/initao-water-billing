<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Restructures water_rates from simple description-based rates to
     * a tiered rate structure with period, class, and consumption ranges.
     */
    public function up(): void
    {
        // Step 1: Drop foreign keys referencing water_rates
        Schema::table('ServiceConnection', function (Blueprint $table) {
            $table->dropForeign(['rate_id']);
        });

        // Step 2: Drop the period_id foreign key and constraints we added earlier
        Schema::table('water_rates', function (Blueprint $table) {
            if (Schema::hasColumn('water_rates', 'period_id')) {
                $table->dropForeign(['period_id']);
                $table->dropUnique('water_rates_period_rate_unique');
            }
            $table->dropForeign(['stat_id']);
        });

        // Step 3: Drop the old water_rates table
        Schema::dropIfExists('water_rates');

        // Step 4: Create new water_rates table with tiered structure
        Schema::create('water_rates', function (Blueprint $table) {
            $table->id('wr_id');
            $table->unsignedBigInteger('period_id')->nullable()->comment('NULL = default rates');
            $table->unsignedBigInteger('class_id')->comment('Links to account_type.at_id');
            $table->unsignedInteger('range_id')->comment('Tier level within class (1, 2, 3, etc.)');
            $table->unsignedInteger('range_min')->comment('Minimum consumption in cu.m');
            $table->unsignedInteger('range_max')->comment('Maximum consumption in cu.m');
            $table->decimal('rate_val', 10, 2)->comment('Base rate value for this tier');
            $table->decimal('rate_inc', 10, 2)->default(0)->comment('Rate increment per cu.m above minimum');
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('period_id')
                ->references('per_id')
                ->on('period')
                ->onDelete('cascade');

            $table->foreign('class_id')
                ->references('at_id')
                ->on('account_type')
                ->onDelete('restrict');

            $table->foreign('stat_id')
                ->references('stat_id')
                ->on('statuses')
                ->onDelete('restrict');

            // Unique constraint: one rate per period + class + range
            $table->unique(['period_id', 'class_id', 'range_id'], 'water_rates_period_class_range_unique');

            // Indexes for lookups
            $table->index(['period_id', 'class_id'], 'water_rates_period_class_index');
        });

        // Step 5: Remove rate_id from ServiceConnection
        Schema::table('ServiceConnection', function (Blueprint $table) {
            if (Schema::hasColumn('ServiceConnection', 'rate_id')) {
                $table->dropColumn('rate_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Drop the new water_rates table
        Schema::dropIfExists('water_rates');

        // Step 2: Recreate the old water_rates structure
        Schema::create('water_rates', function (Blueprint $table) {
            $table->id('wr_id');
            $table->unsignedBigInteger('period_id')->nullable();
            $table->string('rate_desc');
            $table->decimal('rate', 10, 5);
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            $table->foreign('period_id')
                ->references('per_id')
                ->on('period')
                ->onDelete('cascade');

            $table->foreign('stat_id')
                ->references('stat_id')
                ->on('statuses')
                ->onDelete('restrict');

            $table->unique(['period_id', 'rate_desc'], 'water_rates_period_rate_unique');
            $table->index('rate_desc', 'water_rate_desc_index');
        });

        // Step 3: Add rate_id back to ServiceConnection
        Schema::table('ServiceConnection', function (Blueprint $table) {
            $table->unsignedBigInteger('rate_id')->nullable()->after('account_type_id');
            $table->foreign('rate_id')
                ->references('wr_id')
                ->on('water_rates')
                ->onDelete('set null');
        });
    }
};
