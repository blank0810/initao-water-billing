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
    Schema::table('Consumer', function (Blueprint $table) {
      // Add foreign key constraints now that all tables exist
      $table->foreign('cust_id')
        ->references('cust_id')
        ->on('customer')
        ->onDelete('restrict');

      $table->foreign('cm_id')
        ->references('cm_id')
        ->on('consumer_meters')
        ->onDelete('restrict');

      $table->foreign('a_id')
        ->references('a_id')
        ->on('area')
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
    Schema::table('Consumer', function (Blueprint $table) {
      $foreignKeys = ['cust_id', 'cm_id', 'a_id', 'stat_id'];

      foreach ($foreignKeys as $column) {
        $table->dropForeign(["consumer_{$column}_foreign"]);
      }
    });
  }
};
