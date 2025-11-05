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
        Schema::create('meter_reading_old', function (Blueprint $table) {
            $table->id('mro_id');
            $table->unsignedBigInteger('connection_id');
            $table->unsignedBigInteger('meter_id');
            $table->decimal('reading', 10, 2);
            $table->date('reading_date');
            $table->unsignedBigInteger('reader_id');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('stat_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('connection_id')
                  ->references('connection_id')
                  ->on('service_connection')
                  ->onDelete('restrict');
                  
            $table->foreign('meter_id')
                  ->references('mtr_id')
                  ->on('meter')
                  ->onDelete('restrict');
                  
            $table->foreign('reader_id')
                  ->references('mr_id')
                  ->on('meter_reader')
                  ->onDelete('restrict');
                  
            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');
                  
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');
            
            // Add index for search optimization
            $table->index('connection_id', 'meter_reading_old_connection_index');
            $table->index('reading_date', 'meter_reading_old_date_index');
            $table->index('meter_id', 'meter_reading_old_meter_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first if they exist
        Schema::table('meter_reading_old', function (Blueprint $table) {
            $foreignKeys = [
                'connection_id',
                'meter_id',
                'reader_id',
                'stat_id',
                'user_id'
            ];
            
            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('meter_reading_old', $column)) {
                    $table->dropForeign(["meterreadingold_{$column}_foreign"]);
                }
            }
            
            // Drop indexes if they exist
            $indexes = [
                'meter_reading_old_connection_index',
                'meter_reading_old_date_index',
                'meter_reading_old_meter_index'
            ];
            
            foreach ($indexes as $index) {
                if (Schema::hasIndex('meter_reading_old', $index)) {
                    $table->dropIndex($index);
                }
            }
        });
        
        Schema::dropIfExists('meter_reading_old');
    }
};
