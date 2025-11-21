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
    Schema::table('CustomerLedger', function (Blueprint $table) {
      // Add foreign key constraints now that all tables exist
      $table->foreign('customer_id')
        ->references('cust_id')
        ->on('customer')
        ->onDelete('cascade');

      $table->foreign('connection_id')
        ->references('connection_id')
        ->on('ServiceConnection')
        ->onDelete('cascade');

      $table->foreign('period_id')
        ->references('per_id')
        ->on('period')
        ->onDelete('set null');

      $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('set null');

      $table->foreign('stat_id')
        ->references('stat_id')
        ->on('statuses');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('CustomerLedger', function (Blueprint $table) {
      $foreignKeys = ['customer_id', 'connection_id', 'period_id', 'user_id', 'stat_id'];

      foreach ($foreignKeys as $column) {
        $table->dropForeign(["customer_ledger_{$column}_foreign"]);
      }
    });
  }
};
