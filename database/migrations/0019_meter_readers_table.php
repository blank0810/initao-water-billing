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
        Schema::create('meter_readers', function (Blueprint $table) {
            $table->id('mr_id');
            $table->string('mr_name');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index('mr_name', 'meter_reader_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop index if exists
        Schema::table('meter_readers', function (Blueprint $table) {
            if (Schema::hasIndex('meter_readers', 'meter_reader_name_index')) {
                $table->dropIndex('meter_reader_name_index');
            }
        });

        Schema::dropIfExists('meter_readers');
    }
};
