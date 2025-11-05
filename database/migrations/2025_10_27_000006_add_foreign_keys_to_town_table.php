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
    Schema::table('town', function (Blueprint $table) {
      // Add foreign key constraints now that all tables exist
      $table->foreign('prov_id')
        ->references('prov_id')
        ->on('province')
        ->onDelete('restrict');

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
    Schema::table('town', function (Blueprint $table) {
      $foreignKeys = ['prov_id', 'stat_id'];

      foreach ($foreignKeys as $column) {
        if (Schema::hasColumn('town', $column)) {
          $table->dropForeign(["town_{$column}_foreign"]);
        }
      }

      // Drop index if exists
      if (Schema::hasIndex('town', 'town_desc_index')) {
        $table->dropIndex('town_desc_index');
      }
    });
  }
};
