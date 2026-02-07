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
            $table->boolean('is_meter_change')->default(false)->after('computed_amount');
            $table->decimal('removal_read', 12, 3)->nullable()->after('is_meter_change');
            $table->decimal('install_read', 12, 3)->nullable()->after('removal_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uploaded_readings', function (Blueprint $table) {
            $table->dropColumn(['is_meter_change', 'removal_read', 'install_read']);
        });
    }
};
