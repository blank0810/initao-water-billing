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
        Schema::create('province', function (Blueprint $table) {
            $table->id('prov_id');
            $table->string('prov_desc');
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index('prov_desc', 'province_desc_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop index if exists
        Schema::table('province', function (Blueprint $table) {
            if (Schema::hasIndex('province', 'province_desc_index')) {
                $table->dropIndex('province_desc_index');
            }
        });

        Schema::dropIfExists('province');
    }
};
