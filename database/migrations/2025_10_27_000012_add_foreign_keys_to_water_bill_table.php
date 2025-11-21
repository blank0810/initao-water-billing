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
    Schema::table('water_bill', function (Blueprint $table) {
      // Add foreign key constraints now that all tables exist
      $table->foreign('cl_id')
        ->references('cl_id')
        ->on('consumer_ledger')
        ->onDelete('restrict');

      $table->foreign('per_id')
        ->references('per_id')
        ->on('period')
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
    Schema::table('water_bill', function (Blueprint $table) {
      $foreignKeys = ['cl_id', 'per_id', 'stat_id'];

      foreach ($foreignKeys as $column) {
        if (Schema::hasColumn('water_bill', $column)) {
          $table->dropForeign(["water_bill_{$column}_foreign"]);
        }
      }
    });
  }
};
