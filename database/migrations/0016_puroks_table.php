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
        Schema::create('purok', function (Blueprint $table) {
            $table->id('p_id');
            $table->string('p_desc');
            $table->unsignedBigInteger('b_id'); // Barangay ID
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index('p_desc', 'purok_desc_index');
        });

        // Note: Initial purok data should be seeded using database seeders
        // after the initial migration is complete.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop index if exists
        Schema::table('purok', function (Blueprint $table) {
            if (Schema::hasIndex('purok', 'purok_desc_index')) {
                $table->dropIndex('purok_desc_index');
            }
        });

        Schema::dropIfExists('purok');
    }
};
