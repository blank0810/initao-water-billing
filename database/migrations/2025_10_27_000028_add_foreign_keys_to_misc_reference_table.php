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
    Schema::table('misc_reference', function (Blueprint $table) {
      // Add foreign key constraints now that all tables exist
      $table->foreign('stat_id')
        ->references('stat_id')
        ->on('statuses')
        ->onDelete('restrict');

      $table->foreign('created_by')
        ->references('id')
        ->on('users')
        ->onDelete('restrict');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    // Drop foreign keys first if they exist
    Schema::table('misc_reference', function (Blueprint $table) {
      $foreignKeys = ['stat_id', 'created_by'];

      foreach ($foreignKeys as $column) {
        if (Schema::hasColumn('misc_reference', $column)) {
          $table->dropForeign(["miscreference_{$column}_foreign"]);
        }
      }

      // Drop indexes if they exist
      $indexes = [
        'misc_reference_type_index',
        'misc_reference_code_index',
        'misc_reference_active_index'
      ];

      foreach ($indexes as $index) {
        if (Schema::hasIndex('misc_reference', $index)) {
          $table->dropIndex($index);
        }
      }
    });
  }
};
