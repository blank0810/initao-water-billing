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
        Schema::table('consumer_address', function (Blueprint $table) {
            // Add foreign key constraints now that all tables exist
            $table->foreign('p_id')
                ->references('p_id')
                ->on('purok')
                ->onDelete('restrict');

            $table->foreign('b_id')
                ->references('b_id')
                ->on('barangay')
                ->onDelete('restrict');

            $table->foreign('t_id')
                ->references('t_id')
                ->on('town')
                ->onDelete('restrict');

            $table->foreign('prov_id')
                ->references('prov_id')
                ->on('province')
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
        Schema::table('consumer_address', function (Blueprint $table) {
            $foreignKeys = ['p_id', 'b_id', 't_id', 'prov_id', 'stat_id'];

            foreach ($foreignKeys as $column) {
                if (Schema::hasColumn('consumer_address', $column)) {
                    $table->dropForeign(['consumer_address_'.$column.'_foreign']);
                }
            }
        });
    }
};
