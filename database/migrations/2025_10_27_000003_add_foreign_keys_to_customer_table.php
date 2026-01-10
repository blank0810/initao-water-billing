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
        Schema::table('customer', function (Blueprint $table) {
            // Add foreign key constraints now that all tables exist
            $table->foreign('ca_id')
                ->references('ca_id')
                ->on('consumer_address')
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
        Schema::table('customer', function (Blueprint $table) {
            $foreignKeys = ['ca_id', 'stat_id'];

            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('customer', $column)) {
                    $table->dropForeign(['customer_'.$column.'_foreign']);
                }
            }
        });
    }
};
