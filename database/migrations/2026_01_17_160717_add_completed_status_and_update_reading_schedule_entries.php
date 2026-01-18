<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add 'COMPLETED' status to statuses table if it doesn't exist
        $completedExists = DB::table('statuses')->where('stat_desc', 'COMPLETED')->exists();
        if (!$completedExists) {
            DB::table('statuses')->insert([
                'stat_desc' => 'COMPLETED',
            ]);
        }

        // 2. Get status IDs for migration
        $pendingId = DB::table('statuses')->where('stat_desc', 'PENDING')->value('stat_id');
        $completedId = DB::table('statuses')->where('stat_desc', 'COMPLETED')->value('stat_id');

        // 3. Add status_id column to reading_schedule_entries
        Schema::table('reading_schedule_entries', function (Blueprint $table) use ($pendingId) {
            $table->unsignedBigInteger('status_id')->default($pendingId ?? 1)->after('sequence_order');
        });

        // 4. Migrate existing status values to status_id
        if ($pendingId) {
            DB::table('reading_schedule_entries')
                ->where('status', 'pending')
                ->update(['status_id' => $pendingId]);
        }
        if ($completedId) {
            DB::table('reading_schedule_entries')
                ->where('status', 'completed')
                ->orWhere('status', 'COMPLETED')
                ->update(['status_id' => $completedId]);
        }

        // 5. Drop old status column and add foreign key
        Schema::table('reading_schedule_entries', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->foreign('status_id')->references('stat_id')->on('statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add back the status string column
        Schema::table('reading_schedule_entries', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->string('status')->default('pending')->after('sequence_order');
        });

        // 2. Migrate status_id back to status string
        $statuses = DB::table('statuses')->pluck('stat_desc', 'stat_id');
        foreach ($statuses as $id => $desc) {
            DB::table('reading_schedule_entries')
                ->where('status_id', $id)
                ->update(['status' => strtolower($desc)]);
        }

        // 3. Drop status_id column
        Schema::table('reading_schedule_entries', function (Blueprint $table) {
            $table->dropColumn('status_id');
        });

        // 4. Remove COMPLETED status from statuses table
        DB::table('statuses')->where('stat_desc', 'COMPLETED')->delete();
    }
};
