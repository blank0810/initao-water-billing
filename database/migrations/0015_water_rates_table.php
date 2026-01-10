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
        Schema::create('water_rates', function (Blueprint $table) {
            $table->id('wr_id');
            $table->string('rate_desc');
            $table->decimal('rate', 10, 5);
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign key constraint (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index('rate_desc', 'water_rate_desc_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop index if exists
        Schema::table('water_rates', function (Blueprint $table) {
            if (Schema::hasIndex('water_rates', 'water_rate_desc_index')) {
                $table->dropIndex('water_rate_desc_index');
            }
        });

        Schema::dropIfExists('water_rates');
    }
};
