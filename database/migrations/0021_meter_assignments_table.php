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
        Schema::create('MeterAssignment', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->unsignedBigInteger('connection_id');
            $table->unsignedBigInteger('meter_id');
            $table->date('installed_at');
            $table->date('removed_at')->nullable();
            $table->decimal('install_read', 12, 3)->default(0.000);
            $table->decimal('removal_read', 12, 3)->nullable();

            // Foreign key constraints (will be added in a separate migration after all tables are created)
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
