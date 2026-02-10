<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $activeStatusId = \App\Models\Status::getIdByDescription(\App\Models\Status::ACTIVE);

            $table->unsignedBigInteger('stat_id')->default($activeStatusId)->after('connection_id');

            $table->foreign('stat_id')->references('stat_id')->on('statuses');
        });
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
