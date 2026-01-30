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
            $table->string('photo_path', 500)->nullable()->after('is_scanned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uploaded_readings', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};
