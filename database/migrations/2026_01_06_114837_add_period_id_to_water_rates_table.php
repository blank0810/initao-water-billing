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
        Schema::table('water_rates', function (Blueprint $table) {
            // Add nullable period_id column (null = default/legacy rates)
            $table->unsignedBigInteger('period_id')->nullable()->after('wr_id');

            // Add foreign key constraint
            $table->foreign('period_id')
                ->references('per_id')
                ->on('period')
                ->onDelete('cascade');

            // Add unique constraint to prevent duplicate rate descriptions per period
            $table->unique(['period_id', 'rate_desc'], 'water_rates_period_rate_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_rates', function (Blueprint $table) {
            // Drop foreign key and unique constraint first
            $table->dropForeign(['period_id']);
            $table->dropUnique('water_rates_period_rate_unique');

            // Drop the column
            $table->dropColumn('period_id');
        });
    }
};
