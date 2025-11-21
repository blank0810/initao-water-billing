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
    Schema::table('purok', function (Blueprint $table) {
      // Add foreign key constraints now that all tables exist
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
    Schema::table('purok', function (Blueprint $table) {
      if (Schema::hasColumn('purok', 'stat_id')) {
        $table->dropForeign(['purok_stat_id_foreign']);
      }

      // Drop index if exists
      if (Schema::hasIndex('purok', 'purok_desc_index')) {
        $table->dropIndex('purok_desc_index');
      }
    });
  }
};
