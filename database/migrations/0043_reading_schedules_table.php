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
            Schema::create('reading_schedule', function (Blueprint $table) {
                  $table->id('schedule_id');
                  $table->unsignedBigInteger('period_id');
                  $table->unsignedBigInteger('area_id');
                  $table->unsignedBigInteger('reader_id');

                  // Schedule dates
                  $table->date('scheduled_start_date');
                  $table->date('scheduled_end_date');

                  // Actual completion dates (filled when completed)
                  $table->date('actual_start_date')->nullable();
                  $table->date('actual_end_date')->nullable();

                  // Status tracking
                  $table->enum('status', ['pending', 'in_progress', 'completed', 'delayed'])->default('pending');
                  $table->text('notes')->nullable();

                  // Stats
                  $table->integer('total_meters')->default(0);
                  $table->integer('meters_read')->default(0);
                  $table->integer('meters_missed')->default(0);

                  // Audit fields
                  $table->unsignedBigInteger('created_by');
                  $table->unsignedBigInteger('completed_by')->nullable();
                  $table->unsignedBigInteger('stat_id');
                  $table->timestamps();

                  // Foreign key constraints (will be added in a separate migration after all tables are created)

                  // Add index for search optimization
                  $table->index('period_id', 'reading_schedule_period_index');
                  $table->index('area_id', 'reading_schedule_area_index');
                  $table->index('reader_id', 'reading_schedule_reader_index');
                  $table->index('status', 'reading_schedule_status_index');
                  $table->index('scheduled_start_date', 'reading_schedule_start_date_index');
            });
      }

      /**
       * Reverse the migrations.
       */
      public function down(): void
      {
            Schema::dropIfExists('reading_schedule');
      }
};
