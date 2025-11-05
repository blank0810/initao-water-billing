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
    Schema::table('ServiceApplication', function (Blueprint $table) {
      // Add foreign key constraints now that all tables exist
      $table->foreign('customer_id')
        ->references('cust_id')
        ->on('customer')
        ->onDelete('restrict');

      $table->foreign('address_id')
        ->references('ca_id')
        ->on('consumer_address')
        ->onDelete('restrict');

      $table->foreign('approved_by')
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
    // Drop foreign keys first if they exist
    Schema::table('ServiceApplication', function (Blueprint $table) {
      $foreignKeys = ['customer_id', 'address_id', 'approved_by', 'stat_id'];

      foreach ($foreignKeys as $column) {
        if (Schema::hasColumn('ServiceApplication', $column)) {
          $table->dropForeign(["serviceapplication_{$column}_foreign"]);
        }
      }

      // Drop indexes if they exist
      if (Schema::hasIndex('ServiceApplication', 'service_application_number_index')) {
        $table->dropIndex('service_application_number_index');
      }

      if (Schema::hasIndex('ServiceApplication', 'service_application_date_index')) {
        $table->dropIndex('service_application_date_index');
      }
    });
  }
};
