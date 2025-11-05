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
    Schema::table('payment_transactions', function (Blueprint $table) {
      // Add foreign key constraints now that all tables exist
      $table->foreign('payment_id')
        ->references('payment_id')
        ->on('Payment')
        ->onDelete('cascade');

      $table->foreign('bill_id')
        ->references('wb_id')
        ->on('water_bill')
        ->onDelete('set null');

      $table->foreign('processed_by')
        ->references('id')
        ->on('users')
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
    Schema::table('payment_transactions', function (Blueprint $table) {
      $foreignKeys = ['payment_id', 'bill_id', 'processed_by', 'stat_id'];

      foreach ($foreignKeys as $column) {
        $table->dropForeign(["payment_transactions_{$column}_foreign"]);
      }

      // Drop indexes
      $table->dropIndex('payment_transaction_payment_index');
      $table->dropIndex('payment_transaction_bill_index');
      $table->dropIndex('payment_transaction_reference_index');
      $table->dropIndex('payment_transaction_applied_to_index');
    });
  }
};
