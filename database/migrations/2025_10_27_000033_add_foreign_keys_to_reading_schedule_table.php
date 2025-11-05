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
    Schema::table('reading_schedule', function (Blueprint $table) {
      // Add foreign key constraints now that all tables exist
      $table->foreign('period_id')
        ->references('per_id')
        ->on('period')
        ->onDelete('restrict');

      $table->foreign('area_id')
        ->references('a_id')
        ->on('area')
        ->onDelete('restrict');

      $table->foreign('reader_id')
        ->references('mr_id')
        ->on('meter_readers')
        ->onDelete('restrict');

      $table->foreign('created_by')
        ->references('id')
        ->on('users')
        ->onDelete('restrict');

      $table->foreign('completed_by')
        ->references('id')
        ->on('users')
        ->onDelete('set null');

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
    Schema::table('reading_schedule', function (Blueprint $table) {
      $foreignKeys = ['period_id', 'area_id', 'reader_id', 'created_by', 'completed_by', 'stat_id'];

      foreach ($foreignKeys as $column) {
        $table->dropForeign(["reading_schedule_{$column}_foreign"]);
      }

      // Drop indexes
      $table->dropIndex('reading_schedule_period_index');
      $table->dropIndex('reading_schedule_area_index');
      $table->dropIndex('reading_schedule_reader_index');
      $table->dropIndex('reading_schedule_status_index');
      $table->dropIndex('reading_schedule_start_date_index');
    });
  }
};
