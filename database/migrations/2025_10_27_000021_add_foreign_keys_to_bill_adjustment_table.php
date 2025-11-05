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
    Schema::table('BillAdjustment', function (Blueprint $table) {
      // Add foreign key constraints now that all tables exist
      $table->foreign('bill_id')
        ->references('bill_id')
        ->on('water_bill_history')
        ->onDelete('cascade');

      $table->foreign('bill_adjustment_type_id')
        ->references('bill_adjustment_type_id')
        ->on('BillAdjustmentType')
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
    Schema::table('BillAdjustment', function (Blueprint $table) {
      $foreignKeys = ['bill_id', 'bill_adjustment_type_id', 'user_id'];

      foreach ($foreignKeys as $column) {
        $table->dropForeign(["billadjustment_{$column}_foreign"]);
      }

      // Drop indexes
      $table->dropIndex('bill_adjustment_bill_index');
      $table->dropIndex('bill_adjustment_created_at_index');
    });
  }
};
