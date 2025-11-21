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
        Schema::create('consumer_address', function (Blueprint $table) {
            $table->id('ca_id');
            $table->unsignedBigInteger('p_id');
            $table->unsignedBigInteger('b_id');
            $table->unsignedBigInteger('t_id');
            $table->unsignedBigInteger('prov_id');
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();

            // Foreign key constraints (will be added in a separate migration after all tables are created)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumer_address');
    }
};
