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
        Schema::table('MeterAssignment', function (Blueprint $table) {
            // Add foreign key constraints now that all tables exist
            $table->foreign('connection_id')
                ->references('connection_id')
                ->on('ServiceConnection')
                ->onDelete('cascade');

            $table->foreign('meter_id')
                ->references('mtr_id')
                ->on('meter')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('MeterAssignment');
    }
};
