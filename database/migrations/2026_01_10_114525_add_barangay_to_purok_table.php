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
        Schema::table('purok', function (Blueprint $table) {
            $table->unsignedBigInteger('b_id')->nullable()->after('p_desc');
            $table->foreign('b_id')->references('b_id')->on('barangay')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purok', function (Blueprint $table) {
            $table->dropForeign(['b_id']);
            $table->dropColumn('b_id');
        });
    }
};
