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
    Schema::table('meter_reading_old', function (Blueprint $table) {
      // Add foreign key constraints now that all tables exist
      $table->foreign('connection_id')
        ->references('connection_id')
        ->on('ServiceConnection')
        ->onDelete('restrict');

      $table->foreign('meter_id')
        ->references('mtr_id')
        ->on('meter')
        ->onDelete('restrict');

      $table->foreign('reader_id')
        ->references('mr_id')
        ->on('meter_readers')
        ->onDelete('restrict');

      $table->foreign('stat_id')
        ->references('stat_id')
        ->on('statuses')
        ->onDelete('restrict');

      $table->foreign('user_id')
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
    Schema::table('meter_reading_old', function (Blueprint $table) {
      $foreignKeys = ['connection_id', 'meter_id', 'reader_id', 'stat_id', 'user_id'];

      foreach ($foreignKeys as $column) {
        $table->dropForeign(["meter_reading_old_{$column}_foreign"]);
      }

      // Drop indexes
      $table->dropIndex('meter_reading_old_connection_index');
      $table->dropIndex('meter_reading_old_date_index');
      $table->dropIndex('meter_reading_old_meter_index');
    });
  }
};
