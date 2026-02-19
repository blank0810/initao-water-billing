<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Payment', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->nullable()->after('stat_id');
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancelled_at');
            $table->text('cancellation_reason')->nullable()->after('cancelled_by');

            $table->foreign('cancelled_by')->references('id')->on('users')->nullOnDelete();
            $table->index('cancelled_at', 'payment_cancelled_at_index');
        });

        Schema::table('PaymentAllocation', function (Blueprint $table) {
            $table->unsignedBigInteger('stat_id')->nullable()->after('connection_id');
            $table->foreign('stat_id')->references('stat_id')->on('statuses');
        });

        // Backfill existing rows using a DB-level subquery (no model dependency)
        DB::statement("
            UPDATE PaymentAllocation
            SET stat_id = (SELECT stat_id FROM statuses WHERE stat_desc = 'ACTIVE' LIMIT 1)
            WHERE stat_id IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('Payment', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropIndex('payment_cancelled_at_index');
            $table->dropColumn(['cancelled_at', 'cancelled_by', 'cancellation_reason']);
        });

        Schema::table('PaymentAllocation', function (Blueprint $table) {
            $table->dropForeign(['stat_id']);
            $table->dropColumn('stat_id');
        });
    }
};
