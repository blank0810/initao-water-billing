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
    Schema::table('water_bill_adjustment', function (Blueprint $table) {
      // Add foreign key constraints now that all tables exist
      $table->foreign('wb_id')
        ->references('wb_id')
        ->on('water_bill')
        ->onDelete('cascade');

      $table->foreign('approved_by')
        ->references('id')
        ->on('users')
        ->onDelete('set null');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    // Drop foreign keys first if they exist
    Schema::table('water_bill_adjustment', function (Blueprint $table) {
      $foreignKeys = ['wb_id', 'approved_by'];

      foreach ($foreignKeys as $column) {
        if (Schema::hasColumn('water_bill_adjustment', $column)) {
          $table->dropForeign(["water_bill_adjustment_{$column}_foreign"]);
        }
      }

      // Drop indexes if they exist
      if (Schema::hasIndex('water_bill_adjustment', 'water_bill_adjustment_bill_index')) {
        $table->dropIndex('water_bill_adjustment_bill_index');
      }

      if (Schema::hasIndex('water_bill_adjustment', 'water_bill_adjustment_type_index')) {
        $table->dropIndex('water_bill_adjustment_type_index');
      }
    });
  }
};
