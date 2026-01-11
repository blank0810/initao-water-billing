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
        Schema::table('ServiceApplication', function (Blueprint $table) {
            // Verification stage
            $table->dateTime('verified_at')->nullable()->after('approved_at');
            $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');

            // Payment stage
            $table->dateTime('paid_at')->nullable()->after('verified_by');
            $table->unsignedBigInteger('payment_id')->nullable()->after('paid_at');

            // Scheduling stage
            $table->dateTime('scheduled_at')->nullable()->after('payment_id');
            $table->date('scheduled_connection_date')->nullable()->after('scheduled_at');
            $table->unsignedBigInteger('scheduled_by')->nullable()->after('scheduled_connection_date');

            // Connection stage
            $table->dateTime('connected_at')->nullable()->after('scheduled_by');
            $table->unsignedBigInteger('connection_id')->nullable()->after('connected_at');

            // Rejection stage
            $table->dateTime('rejected_at')->nullable()->after('connection_id');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
            $table->text('rejection_reason')->nullable()->after('rejected_by');

            // Cancellation stage
            $table->dateTime('cancelled_at')->nullable()->after('rejection_reason');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');

            // Foreign keys
            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('payment_id')->references('payment_id')->on('Payment')->nullOnDelete();
            $table->foreign('scheduled_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('connection_id')->references('connection_id')->on('ServiceConnection')->nullOnDelete();
            $table->foreign('rejected_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ServiceApplication', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['payment_id']);
            $table->dropForeign(['scheduled_by']);
            $table->dropForeign(['connection_id']);
            $table->dropForeign(['rejected_by']);

            // Drop columns
            $table->dropColumn([
                'verified_at',
                'verified_by',
                'paid_at',
                'payment_id',
                'scheduled_at',
                'scheduled_connection_date',
                'scheduled_by',
                'connected_at',
                'connection_id',
                'rejected_at',
                'rejected_by',
                'rejection_reason',
                'cancelled_at',
                'cancellation_reason',
            ]);
        });
    }
};
