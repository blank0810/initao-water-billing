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
        Schema::create('meter_assignment', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->unsignedBigInteger('connection_id');
            $table->unsignedBigInteger('meter_id');
            $table->date('installed_at');
            $table->date('removed_at')->nullable();
            $table->decimal('install_read', 10, 3);
            $table->decimal('removal_read', 10, 3)->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('connection_id')
                  ->references('connection_id')
                  ->on('service_connection')
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
        // Drop foreign keys first if they exist
        Schema::table('meter_assignment', function (Blueprint $table) {
            if (Schema::hasColumn('meter_assignment', 'connection_id')) {
                $table->dropForeign(['connection_id']);
            }
            if (Schema::hasColumn('meter_assignment', 'meter_id')) {
                $table->dropForeign(['meter_id']);
            }
        });
        
        Schema::dropIfExists('meter_assignment');
    }
};
