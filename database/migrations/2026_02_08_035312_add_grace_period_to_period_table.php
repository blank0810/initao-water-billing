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
        Schema::table('period', function (Blueprint $table) {
            $table->unsignedInteger('grace_period')->default(10)->after('end_date')
                ->comment('Number of days after end_date before penalties apply');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('period', function (Blueprint $table) {
            $table->dropColumn('grace_period');
        });
    }
};
