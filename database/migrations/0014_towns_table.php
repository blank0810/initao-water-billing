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
        Schema::create('town', function (Blueprint $table) {
            $table->id('t_id');
            $table->string('t_desc');
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index('t_desc', 'town_desc_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop index if exists
        Schema::table('town', function (Blueprint $table) {
            if (Schema::hasIndex('town', 'town_desc_index')) {
                $table->dropIndex('town_desc_index');
            }
        });

        Schema::dropIfExists('town');
    }
};
