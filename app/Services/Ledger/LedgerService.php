<?php

namespace App\Services\Ledger;

use App\Models\CustomerCharge;
use App\Models\CustomerLedger;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\Status;
use Illuminate\Support\Collection;

class LedgerService
{
    /**
     * Record a charge as a DEBIT entry in the ledger
     *
     * Called when charges are created (at verification)
     *
     * @param  int|null  $periodId  Optional period ID (e.g., for penalties tied to a specific billing period)
     */
    public function recordCharge(CustomerCharge $charge, int $userId, ?int $periodId = null): CustomerLedger
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        return CustomerLedger::create([
            'customer_id' => $charge->customer_id,
            'connection_id' => $charge->connection_id, // May be null for applications
            'period_id' => $periodId, // Null for one-time charges, set for period-specific charges like penalties
            'txn_date' => now()->toDateString(),
            'post_ts' => now(),
            'source_type' => 'CHARGE',
            'source_id' => $charge->charge_id,
            'source_line_no' => 1,
            'description' => $charge->description,
            'debit' => $charge->total_amount,
            'credit' => 0,
            'user_id' => $userId,
            'stat_id' => $activeStatusId,
        ]);
    }

    /**
     * Record multiple charges as DEBIT entries
     */
    public function recordCharges(Collection $charges, int $userId): Collection
    {
        return $charges->map(fn ($charge) => $this->recordCharge($charge, $userId));
    }

    /**
     * Record a payment allocation as a CREDIT entry in the ledger
     *
     * Called when payment is processed
     */
    public function recordPaymentAllocation(
        PaymentAllocation $allocation,
        Payment $payment,
        string $description,
        int $userId
    ): CustomerLedger {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        return CustomerLedger::create([
            'customer_id' => $payment->payer_id,
            'connection_id' => $allocation->connection_id,
            'period_id' => $allocation->period_id,
            'txn_date' => $payment->payment_date,
            'post_ts' => now(),
            'source_type' => 'PAYMENT',
            'source_id' => $payment->payment_id,
            'source_line_no' => $allocation->payment_allocation_id,
            'description' => $description,
            'debit' => 0,
            'credit' => $allocation->amount_applied,
            'user_id' => $userId,
            'stat_id' => $activeStatusId,
        ]);
    }

    /**
     * Get customer ledger balance
     */
    public function getCustomerBalance(int $customerId): float
    {
        $totals = CustomerLedger::where('customer_id', $customerId)
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        return (float) (($totals->total_debit ?? 0) - ($totals->total_credit ?? 0));
    }

    /**
     * Get ledger entries for a customer
     *
     * Ordered newest-first with ledger_entry_id DESC for consistent ordering
     * within same timestamp, making balance flow naturally when reading top-to-bottom
     */
    public function getCustomerLedgerEntries(int $customerId): Collection
    {
        return CustomerLedger::with(['status', 'user'])
            ->where('customer_id', $customerId)
            ->orderBy('txn_date', 'desc')
            ->orderBy('post_ts', 'desc')
            ->orderBy('ledger_entry_id', 'desc')
            ->get();
    }

    /**
     * Get ledger entries for an application (via charges)
     */
    public function getApplicationLedgerEntries(int $applicationId): Collection
    {
        $chargeIds = CustomerCharge::where('application_id', $applicationId)
            ->pluck('charge_id');

        return CustomerLedger::with(['status', 'user'])
            ->where(function ($query) use ($chargeIds) {
                $query->where('source_type', 'CHARGE')
                    ->whereIn('source_id', $chargeIds);
            })
            ->orWhere(function ($query) use ($chargeIds) {
                // Get payments that allocated to these charges
                $paymentIds = PaymentAllocation::where('target_type', 'CHARGE')
                    ->whereIn('target_id', $chargeIds)
                    ->pluck('payment_id');

                $query->where('source_type', 'PAYMENT')
                    ->whereIn('source_id', $paymentIds);
            })
            ->orderBy('txn_date', 'desc')
            ->orderBy('post_ts', 'desc')
            ->orderBy('ledger_entry_id', 'desc')
            ->get();
    }
}
