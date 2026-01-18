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
        Schema::table('uploaded_readings', function (Blueprint $table) {
            $table->decimal('computed_amount', 12, 2)->nullable()->after('site_bill_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uploaded_readings', function (Blueprint $table) {
            $table->dropColumn('computed_amount');
        });
    }
};
