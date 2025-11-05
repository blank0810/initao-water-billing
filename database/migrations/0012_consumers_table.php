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
        Schema::create('consumer', function (Blueprint $table) {
            $table->id('c_id');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('email')->unique()->nullable();
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');
                  
            // Add index for search optimization
            $table->index(['last_name', 'first_name'], 'consumer_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key first if it exists
        Schema::table('consumer', function (Blueprint $table) {
            if (Schema::hasColumn('consumer', 'stat_id')) {
                $table->dropForeign(['stat_id']);
            }
            
            // Drop index if it exists
            if (Schema::hasIndex('consumer', 'consumer_name_index')) {
                $table->dropIndex('consumer_name_index');
            }
        });
        
        Schema::dropIfExists('consumer');
    }
};
