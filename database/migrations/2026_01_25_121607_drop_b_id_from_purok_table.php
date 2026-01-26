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
            // Drop foreign key if exists
            if (Schema::hasColumn('purok', 'b_id')) {
                // Try to drop foreign key constraint (may not exist)
                try {
                    $table->dropForeign(['b_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }

                $table->dropColumn('b_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purok', function (Blueprint $table) {
            $table->unsignedBigInteger('b_id')->nullable()->after('p_desc');
            $table->foreign('b_id')->references('b_id')->on('barangay')->onDelete('set null');
        });
    }
};
