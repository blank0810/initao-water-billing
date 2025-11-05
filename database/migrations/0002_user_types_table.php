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
        Schema::create('user_types', function (Blueprint $table) {
            $table->id('ut_id');
            $table->string('ut_desc');
            $table->unsignedBigInteger('stat_id');
            $table->timestamps();
        });

        // Add the foreign key constraint separately
        Schema::table('user_types', function (Blueprint $table) {
            $table->foreign('stat_id')
                  ->references('stat_id')
                  ->on('statuses')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key first if it exists
        Schema::table('user_types', function (Blueprint $table) {
            if (Schema::hasColumn('user_types', 'stat_id')) {
                $table->dropForeign(['stat_id']);
            }
        });
        
        Schema::dropIfExists('user_types');
    }
};
