<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Make period_id and connection_id nullable to support one-time charges
     * like application fees that are not tied to a billing period or connection.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE `PaymentAllocation` MODIFY `period_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `PaymentAllocation` MODIFY `connection_id` BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `PaymentAllocation` MODIFY `period_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `PaymentAllocation` MODIFY `connection_id` BIGINT UNSIGNED NOT NULL');
    }
};
