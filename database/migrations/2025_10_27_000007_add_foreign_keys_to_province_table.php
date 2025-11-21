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
    Schema::table('province', function (Blueprint $table) {
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
    Schema::table('province', function (Blueprint $table) {
      // Drop foreign key first if it exists
      if (Schema::hasColumn('province', 'stat_id')) {
        $table->dropForeign(['province_stat_id_foreign']);
      }

      // Drop index if exists
      if (Schema::hasIndex('province', 'province_desc_index')) {
        $table->dropIndex('province_desc_index');
      }
    });
  }
};
