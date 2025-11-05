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
    Schema::table('AreaAssignment', function (Blueprint $table) {
      // Add foreign key constraints now that all tables exist
      $table->foreign('area_id')
        ->references('a_id')
        ->on('area')
        ->onDelete('restrict');

      $table->foreign('meter_reader_id')
        ->references('mr_id')
        ->on('meter_readers')
        ->onDelete('restrict');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('AreaAssignment', function (Blueprint $table) {
      $table->dropForeign(['area_id']);
      $table->dropForeign(['meter_reader_id']);
      $table->dropIndex(['area_id', 'meter_reader_id', 'effective_from']);
    });
  }
};
