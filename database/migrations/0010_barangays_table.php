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
        Schema::create('barangay', function (Blueprint $table) {
            $table->id('b_id');
            $table->string('b_desc');
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign key constraint (will be added in a separate migration after all tables are created)

            // Add index for performance
            $table->index('b_desc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangay', function (Blueprint $table) {
            $table->dropIndex(['b_desc']);
        });

        Schema::dropIfExists('barangay');
    }
};
