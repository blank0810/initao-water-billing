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
        Schema::table('consumer_ledger', function (Blueprint $table) {
            // Add foreign key constraints now that all tables exist
            $table->foreign('c_id')
                ->references('c_id')
                ->on('Consumer')
                ->onDelete('restrict');

            $table->foreign('stat_id')
                ->references('stat_id')
                ->on('statuses')
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
        Schema::table('consumer_ledger', function (Blueprint $table) {
            $foreignKeys = ['c_id', 'stat_id', 'user_id'];

            foreach ($foreignKeys as $column) {
                $table->dropForeign(["consumer_ledger_{$column}_foreign"]);
            }
        });
    }
};
