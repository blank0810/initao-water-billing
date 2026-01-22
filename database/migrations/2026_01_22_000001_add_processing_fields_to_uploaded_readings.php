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
            // Processing tracking fields
            $table->boolean('is_processed')->default(false)->after('is_scanned');
            $table->timestamp('processed_at')->nullable()->after('is_processed');
            $table->unsignedBigInteger('processed_by')->nullable()->after('processed_at');
            $table->unsignedBigInteger('bill_id')->nullable()->after('processed_by');

            // Foreign key references
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('bill_id')->references('bill_id')->on('water_bill_history')->onDelete('set null');

            // Index for quick filtering
            $table->index('is_processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uploaded_readings', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
            $table->dropForeign(['bill_id']);
            $table->dropIndex(['is_processed']);
            $table->dropColumn(['is_processed', 'processed_at', 'processed_by', 'bill_id']);
        });
    }
};
