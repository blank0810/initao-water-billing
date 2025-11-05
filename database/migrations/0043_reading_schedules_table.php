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
            
            // Foreign key constraints
            $table->foreign('period_id')
                  ->references('per_id')
                  ->on('period')
                  ->onDelete('restrict');
                  
            $table->foreign('area_id')
                  ->references('a_id')
                  ->on('area')
                  ->onDelete('restrict');
                  
            $table->foreign('reader_id')
                  ->references('mr_id')
                  ->on('meter_reader')
                  ->onDelete('restrict');
                  
            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');
                  
            $table->foreign('completed_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');
            
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
        // Drop foreign keys first if they exist
        Schema::table('reading_schedule', function (Blueprint $table) {
            $foreignKeys = [
                'period_id',
                'area_id',
                'reader_id',
                'created_by',
                'completed_by',
                'stat_id'
            ];
            
            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('reading_schedule', $column)) {
                    $table->dropForeign(["readingschedule_{$column}_foreign"]);
                }
            }
            
            // Drop indexes if they exist
            $indexes = [
                'reading_schedule_period_index',
                'reading_schedule_area_index',
                'reading_schedule_reader_index',
                'reading_schedule_status_index',
                'reading_schedule_start_date_index'
            ];
            
            foreach ($indexes as $index) {
                if (Schema::hasIndex('reading_schedule', $index)) {
                    $table->dropIndex($index);
                }
            }
        });
        
        Schema::dropIfExists('reading_schedule');
    }
};
