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
    Schema::table('Payment', function (Blueprint $table) {
      // Add foreign key constraints now that all tables exist
      $table->foreign('payer_id')
        ->references('cust_id')
        ->on('customer')
        ->onDelete('restrict');

      $table->foreign('user_id')
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
    Schema::table('Payment', function (Blueprint $table) {
      $foreignKeys = ['payer_id', 'user_id', 'stat_id'];

      foreach ($foreignKeys as $column) {
        $table->dropForeign(["payment_{$column}_foreign"]);
      }

      // Drop indexes
      $table->dropIndex('payment_receipt_no_index');
      $table->dropIndex('payment_date_index');
    });
  }
};
