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
        Schema::create('statuses', function (Blueprint $table) {
            $table->id('stat_id');
            $table->string('stat_desc');
        });
        
        // Insert default statuses
        DB::table('statuses')->insert([
            ['stat_desc' => 'PENDING'],
            ['stat_desc' => 'ACTIVE'],
            ['stat_desc' => 'INACTIVE'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys in other tables first
        if (Schema::hasTable('user_types')) {
            Schema::table('user_types', function (Blueprint $table) {
                if (Schema::hasColumn('user_types', 'status_id')) {
                    $table->dropForeign(['status_id']);
                }
            });
        }
        
        Schema::dropIfExists('statuses');
    }
};
