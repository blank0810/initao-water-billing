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
        Schema::create('AreaAssignment', function (Blueprint $table) {
            $table->id('area_assignment_id');
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('meter_reader_id');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            
            // Foreign key constraints
            $table->foreign('area_id')
                  ->references('a_id')
                  ->on('area')
                  ->onDelete('restrict');
                  
            // Note: meter_reader_id foreign key will be added after meter_readers table is created
            // $table->foreign('meter_reader_id')
            //       ->references('mr_id')
            //       ->on('meter_readers')
            //       ->onDelete('restrict');
            
            // Add index for performance
            $table->index(['area_id', 'meter_reader_id', 'effective_from']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('AreaAssignment', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            if (Schema::hasTable('meter_readers')) {
                $table->dropForeign(['meter_reader_id']);
            }
            $table->dropIndex(['area_id', 'meter_reader_id', 'effective_from']);
        });
        
        Schema::dropIfExists('AreaAssignment');
    }
};
