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
        Schema::create('consumer_meters', function (Blueprint $table) {
            $table->id('cm_id');
            $table->unsignedBigInteger('mr_id'); // Meter Reader ID
            $table->dateTime('create_date');
            $table->dateTime('install_date');
            $table->decimal('initial_readout', 10, 2);
            $table->decimal('last_reading', 10, 2)->nullable();
            $table->dateTime('pulled_out_at')->nullable();
            $table->unsignedBigInteger('stat_id');
            $table->unsignedBigInteger('user_id'); // User who created the record
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)

            // Add index for search optimization
            $table->index('create_date', 'consumer_meter_create_date_index');
            $table->index('install_date', 'consumer_meter_install_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumer_meters');
    }
};
