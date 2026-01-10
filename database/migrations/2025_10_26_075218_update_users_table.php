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
        Schema::table('users', function (Blueprint $table) {
            // Add new columns
            $table->string('username')->unique()->after('id');
            $table->unsignedBigInteger('u_type')->after('password');
            $table->unsignedBigInteger('stat_id')->after('u_type');

            // Modify existing columns to match model
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
        });

        // Add foreign keys separately to ensure tables exist
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('u_type')
                ->references('ut_id')
                ->on('user_types')
                ->onDelete('restrict');

            $table->foreign('stat_id')
                ->references('stat_id')
                ->on('statuses')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys first if they exist
            if (Schema::hasColumn('users', 'u_type')) {
                $table->dropForeign(['u_type']);
            }
            if (Schema::hasColumn('users', 'stat_id')) {
                $table->dropForeign(['stat_id']);
            }

            // Drop added columns if they exist
            $columnsToDrop = [];
            if (Schema::hasColumn('users', 'username')) {
                $columnsToDrop[] = 'username';
            }
            if (Schema::hasColumn('users', 'u_type')) {
                $columnsToDrop[] = 'u_type';
            }
            if (Schema::hasColumn('users', 'stat_id')) {
                $columnsToDrop[] = 'stat_id';
            }

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }

            // Revert column modifications
            $table->string('name')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
