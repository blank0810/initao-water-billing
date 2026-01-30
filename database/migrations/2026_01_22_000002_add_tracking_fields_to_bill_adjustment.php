<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds fields to track original and adjusted values for consumption and amount.
     */
    public function up(): void
    {
        // Add columns if they don't exist
        if (! Schema::hasColumn('BillAdjustment', 'old_consumption')) {
            Schema::table('BillAdjustment', function (Blueprint $table) {
                $table->decimal('old_consumption', 10, 3)->nullable()->after('amount');
            });
        }

        if (! Schema::hasColumn('BillAdjustment', 'new_consumption')) {
            Schema::table('BillAdjustment', function (Blueprint $table) {
                $table->decimal('new_consumption', 10, 3)->nullable()->after('old_consumption');
            });
        }

        if (! Schema::hasColumn('BillAdjustment', 'old_amount')) {
            Schema::table('BillAdjustment', function (Blueprint $table) {
                $table->decimal('old_amount', 12, 2)->nullable()->after('new_consumption');
            });
        }

        if (! Schema::hasColumn('BillAdjustment', 'new_amount')) {
            Schema::table('BillAdjustment', function (Blueprint $table) {
                $table->decimal('new_amount', 12, 2)->nullable()->after('old_amount');
            });
        }

        if (! Schema::hasColumn('BillAdjustment', 'adjustment_category')) {
            Schema::table('BillAdjustment', function (Blueprint $table) {
                $table->enum('adjustment_category', ['consumption', 'amount', 'both', 'other'])->default('other')->after('new_amount');
            });
        }

        if (! Schema::hasColumn('BillAdjustment', 'stat_id')) {
            Schema::table('BillAdjustment', function (Blueprint $table) {
                $table->unsignedBigInteger('stat_id')->nullable()->after('adjustment_category');
            });
        }

        // Check if foreign key already exists before adding
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite: Just add the foreign key, Laravel handles duplicates gracefully
            try {
                Schema::table('BillAdjustment', function (Blueprint $table) {
                    $table->foreign('stat_id')->references('stat_id')->on('statuses')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key already exists, skip
            }
        } else {
            // MySQL: Check information_schema first
            $foreignKeyExists = DB::select("
                SELECT COUNT(*) as count
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = DATABASE()
                AND TABLE_NAME = 'BillAdjustment'
                AND CONSTRAINT_NAME = 'billadjustment_stat_id_foreign'
            ");

            if ($foreignKeyExists[0]->count == 0) {
                Schema::table('BillAdjustment', function (Blueprint $table) {
                    $table->foreign('stat_id')->references('stat_id')->on('statuses')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if foreign key exists before dropping
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite: Just try to drop, ignore if doesn't exist
            try {
                Schema::table('BillAdjustment', function (Blueprint $table) {
                    $table->dropForeign(['stat_id']);
                });
            } catch (\Exception $e) {
                // Foreign key doesn't exist, skip
            }
        } else {
            // MySQL: Check information_schema first
            $foreignKeyExists = DB::select("
                SELECT COUNT(*) as count
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = DATABASE()
                AND TABLE_NAME = 'BillAdjustment'
                AND CONSTRAINT_NAME = 'billadjustment_stat_id_foreign'
            ");

            if ($foreignKeyExists[0]->count > 0) {
                Schema::table('BillAdjustment', function (Blueprint $table) {
                    $table->dropForeign(['stat_id']);
                });
            }
        }

        Schema::table('BillAdjustment', function (Blueprint $table) {
            $columns = ['old_consumption', 'new_consumption', 'old_amount', 'new_amount', 'adjustment_category', 'stat_id'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('BillAdjustment', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
