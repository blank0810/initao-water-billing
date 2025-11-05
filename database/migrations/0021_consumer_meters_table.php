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
        Schema::create('consumer_meter', function (Blueprint $table) {
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
            
            // Foreign key constraints
            $table->foreign('mr_id')
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
            $table->index('create_date', 'consumer_meter_create_date_index');
            $table->index('install_date', 'consumer_meter_install_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first if they exist
        Schema::table('consumer_meter', function (Blueprint $table) {
            $foreignKeys = ['mr_id', 'stat_id', 'user_id'];
            
            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('consumer_meter', $column)) {
                    $table->dropForeign(["consumer_meters_{$column}_foreign"]);
                }
            }
            
            // Drop indexes if they exist
            if (Schema::hasIndex('consumer_meter', 'consumer_meter_create_date_index')) {
                $table->dropIndex('consumer_meter_create_date_index');
            }
            
            if (Schema::hasIndex('consumer_meter', 'consumer_meter_install_date_index')) {
                $table->dropIndex('consumer_meter_install_date_index');
            }
        });
        
        Schema::dropIfExists('consumer_meter');
    }
};
