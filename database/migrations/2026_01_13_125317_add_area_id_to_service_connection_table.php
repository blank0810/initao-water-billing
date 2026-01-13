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
        Schema::table('ServiceConnection', function (Blueprint $table) {
            $table->unsignedBigInteger('area_id')->nullable()->after('account_type_id');

            $table->foreign('area_id')
                ->references('a_id')
                ->on('area')
                ->onDelete('set null');

            $table->index('area_id', 'service_connection_area_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ServiceConnection', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropIndex('service_connection_area_index');
            $table->dropColumn('area_id');
        });
    }
};
