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
        Schema::table('water_rates', function (Blueprint $table) {
            $table->dropColumn('rate_inc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_rates', function (Blueprint $table) {
            $table->decimal('rate_inc', 10, 2)->nullable()->after('rate_val');
        });
    }
};
