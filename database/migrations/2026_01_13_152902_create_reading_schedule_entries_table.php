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
        Schema::create('reading_schedule_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_id');
            $table->unsignedBigInteger('connection_id');
            // Optional: sequence/order for the route
            $table->integer('sequence_order')->default(0);
            // Optional: status per entry (pending, read, skipped)
            $table->string('status')->default('pending'); 
            
            $table->timestamps();

            // Foreign keys
            $table->foreign('schedule_id')->references('schedule_id')->on('reading_schedule')->onDelete('cascade');
            $table->foreign('connection_id')->references('connection_id')->on('ServiceConnection')->onDelete('cascade');

            // Unique constraint to prevent duplicate entries for the same schedule
            $table->unique(['schedule_id', 'connection_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_schedule_entries');
    }
};