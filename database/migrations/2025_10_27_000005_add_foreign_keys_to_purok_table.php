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
      $table->foreign('b_id')
        ->references('b_id')
        ->on('barangay')
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
    Schema::table('purok', function (Blueprint $table) {
      $foreignKeys = ['b_id', 'stat_id'];

      foreach ($foreignKeys as $column) {
        if (Schema::hasColumn('purok', $column)) {
          $table->dropForeign(["purok_{$column}_foreign"]);
        }
      }

      // Drop index if exists
      if (Schema::hasIndex('purok', 'purok_desc_index')) {
        $table->dropIndex('purok_desc_index');
      }
    });
  }
};
