<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Update CustomerCharge rows that have application_id but no connection_id
        // by looking up ServiceApplication.connection_id
        DB::statement('
            UPDATE CustomerCharge cc
            INNER JOIN ServiceApplication sa ON cc.application_id = sa.application_id
            SET cc.connection_id = sa.connection_id
            WHERE cc.connection_id IS NULL
            AND sa.connection_id IS NOT NULL
        ');

        // Step 2: Update CustomerLedger CHARGE entries that reference those charges
        DB::statement('
            UPDATE CustomerLedger cl
            INNER JOIN CustomerCharge cc ON cl.source_id = cc.charge_id AND cl.source_type = "CHARGE"
            SET cl.connection_id = cc.connection_id
            WHERE cl.connection_id IS NULL
            AND cc.connection_id IS NOT NULL
        ');

        // Step 3: Update PaymentAllocation entries for those charges
        DB::statement('
            UPDATE PaymentAllocation pa
            INNER JOIN CustomerCharge cc ON pa.target_id = cc.charge_id AND pa.target_type = "CHARGE"
            SET pa.connection_id = cc.connection_id
            WHERE pa.connection_id IS NULL
            AND cc.connection_id IS NOT NULL
        ');

        // Step 4: Update CustomerLedger PAYMENT entries that correspond to the updated allocations
        DB::statement('
            UPDATE CustomerLedger cl
            INNER JOIN PaymentAllocation pa ON cl.source_id = pa.payment_id
                AND cl.source_type = "PAYMENT"
                AND cl.source_line_no = pa.payment_allocation_id
            SET cl.connection_id = pa.connection_id
            WHERE cl.connection_id IS NULL
            AND pa.connection_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        // This is a data backfill — rolling back would re-null the connection_ids
        // but we can't distinguish which were originally null vs backfilled.
        // Intentionally left empty for safety.
    }
};
