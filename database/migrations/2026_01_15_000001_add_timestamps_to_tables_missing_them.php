<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that need timestamps added.
     * These tables were created without created_at/updated_at columns.
     */
    protected array $tablesNeedingTimestamps = [
        'area',
        'AreaAssignment',
        'CustomerLedger',
        'MeterAssignment',
        'MeterReading',
        'meter',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tablesNeedingTimestamps as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $blueprint) use ($table) {
                    // Only add if columns don't exist
                    if (! Schema::hasColumn($table, 'created_at')) {
                        $blueprint->timestamp('created_at')->nullable();
                    }
                    if (! Schema::hasColumn($table, 'updated_at')) {
                        $blueprint->timestamp('updated_at')->nullable();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tablesNeedingTimestamps as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $blueprint) use ($table) {
                    if (Schema::hasColumn($table, 'created_at')) {
                        $blueprint->dropColumn('created_at');
                    }
                    if (Schema::hasColumn($table, 'updated_at')) {
                        $blueprint->dropColumn('updated_at');
                    }
                });
            }
        }
    }
};
