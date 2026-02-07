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
            $table->boolean('change_meter')->default(false)->after('ended_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ServiceConnection', function (Blueprint $table) {
            $table->dropColumn('change_meter');
        });
    }
};
