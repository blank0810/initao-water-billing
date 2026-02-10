# Fix Penalty Charge Duplication in Payment Management Queue

**Date:** 2026-02-10
**Branch:** `ledger-bug-fix`
**Status:** Planning

---

## Problem

When a connection has multiple unpaid bills across different periods, penalty charges appear **duplicated** on every bill row in the payment management queue. This happens because `PaymentManagementService::getPendingWaterBills()` groups charges by `connection_id` only — so all charges for a connection appear on each bill row.

**Example:**
- Connection #3 has Bill A (January) and Bill B (February)
- A penalty charge exists for Bill A
- Current behavior: Both bill rows show the penalty in the "Other Charges" badge
- Expected behavior: Only Bill A's row shows the penalty

## Agreed Approach

Use `period_id` from `CustomerLedger` CHARGE entries to associate charges with specific bills. Since `water_bill_history` has `UNIQUE(connection_id, period_id)`, the pair `(connection_id, period_id)` uniquely identifies a bill.

**Dependency:** Another developer is updating `PenaltyService` + `LedgerService` to set `period_id` on CHARGE ledger entries. Our scope is the payment management / display side.

---

## Scope

### In Scope (Our Changes)
1. `PaymentManagementService::getPendingWaterBills()` — fix charge grouping
2. `PaymentService::getConnectionOutstandingItems()` — associate charges with their bills
3. `PaymentService::processConnectionPayment()` — set `period_id` on charge payment allocations
4. `process-water-bill.blade.php` — group charges visually under their bills

### Out of Scope (Other Developer)
- `LedgerService::recordCharge()` — accept and set `period_id`
- `PenaltyService::createPenalty()` — pass `bill->period_id` to ledger
- Backfill existing CHARGE ledger entries with correct `period_id`

---

## Implementation Tasks

### Task 1: Update PaymentManagementService — Fix Charge Grouping

**File:** `app/Services/Payment/PaymentManagementService.php`
**Method:** `getPendingWaterBills()` (lines 138-154)

**Current code (broken):**
```php
$chargesByConnection = CustomerCharge::where('stat_id', $activeStatusId)
    ->whereNull('application_id')
    ->whereIn('connection_id', $connectionIds)
    ->get()
    ->groupBy('connection_id');
```

**New approach:**
```php
// JOIN to CustomerLedger to get period_id for each charge
$chargesWithPeriod = CustomerCharge::join('CustomerLedger', function ($join) {
        $join->on('CustomerCharge.charge_id', '=', 'CustomerLedger.source_id')
            ->where('CustomerLedger.source_type', '=', 'CHARGE');
    })
    ->where('CustomerCharge.stat_id', $activeStatusId)
    ->whereNull('CustomerCharge.application_id')
    ->whereIn('CustomerLedger.connection_id', $connectionIds)
    ->select('CustomerCharge.*', 'CustomerLedger.period_id as ledger_period_id')
    ->get();

// Group by connection_id + period_id (= unique bill identifier)
$chargesByBill = $chargesWithPeriod
    ->groupBy(fn ($c) => $c->connection_id . '-' . $c->ledger_period_id)
    ->map(function ($charges) {
        $unpaid = $charges->filter(fn ($c) => $c->remaining_amount > 0);
        return [
            'count' => $unpaid->count(),
            'total' => $unpaid->sum(fn ($c) => $c->remaining_amount),
        ];
    });
```

**When mapping bill rows (line 161):**
```php
// Before:
$connCharges = $chargesByConnection->get($bill->connection_id, ...);

// After — use bill's (connection_id, period_id) as key:
$billKey = $bill->connection_id . '-' . $bill->period_id;
$connCharges = $chargesByBill->get($billKey, ['count' => 0, 'total' => 0]);
```

**Edge case — charges with NULL period_id in ledger:**
Charges created before the penalty dev's fix will have `period_id = null` in their ledger entry. These should still appear. Strategy: group null-period charges separately and attach them to the **oldest** bill row for that connection (or sum into a fallback bucket).

**Verification:** Load payment queue with a connection that has 2+ bills. Penalty should appear only on the specific bill's row, not on all rows.

---

### Task 2: Update PaymentService::getConnectionOutstandingItems — Associate Charges with Bills

**File:** `app/Services/Payment/PaymentService.php`
**Method:** `getConnectionOutstandingItems()` (lines 200-253)

**Current:** Returns `bills` and `charges` as flat separate arrays. Charges have no bill association:
```php
'period_id' => null,  // line 241 — always null for charges
```

**Change:** JOIN to CustomerLedger to retrieve `period_id` for each charge and include it in the returned data:
```php
// Before: charges query (lines 225-244)
$charges = CustomerCharge::with(['chargeItem', 'status'])
    ->where('connection_id', $connectionId)
    ->whereNull('application_id')
    ->where('stat_id', $activeStatusId)
    ->orderBy('due_date', 'asc')
    ->get()
    ->filter(fn ($charge) => $charge->remaining_amount > 0)
    ->map(function ($charge) {
        return [
            // ...
            'period_id' => null,    // ← always null
        ];
    });

// After: JOIN ledger to get period_id
$charges = CustomerCharge::leftJoin('CustomerLedger', function ($join) {
        $join->on('CustomerCharge.charge_id', '=', 'CustomerLedger.source_id')
            ->where('CustomerLedger.source_type', '=', 'CHARGE');
    })
    ->where('CustomerCharge.connection_id', $connectionId)
    ->whereNull('CustomerCharge.application_id')
    ->where('CustomerCharge.stat_id', $activeStatusId)
    ->select('CustomerCharge.*', 'CustomerLedger.period_id as ledger_period_id')
    ->orderBy('CustomerCharge.due_date', 'asc')
    ->get()
    ->filter(fn ($charge) => $charge->remaining_amount > 0)
    ->map(function ($charge) {
        return [
            // ...
            'period_id' => $charge->ledger_period_id,  // ← from ledger
        ];
    });
```

**Verification:** Process payment page should show charges with correct period association. The frontend (Task 4) will use this to group charges under bills.

---

### Task 3: Update PaymentService::processConnectionPayment — Set period_id on Charge Allocations

**File:** `app/Services/Payment/PaymentService.php`
**Method:** `processConnectionPayment()` (lines 471-493)

**Current:** When creating PaymentAllocation for charges, `period_id` is null:
```php
$allocation = PaymentAllocation::create([
    'payment_id' => $payment->payment_id,
    'target_type' => 'CHARGE',
    'target_id' => $charge->charge_id,
    'amount_applied' => $chargeAmount,
    'period_id' => null,          // ← always null
    'connection_id' => $connectionId,
]);
```

**Change:** Retrieve `period_id` from the charge's ledger entry and set it on the allocation for consistency. This ensures payment ledger entries also have correct `period_id`.

**Approach:** Pre-fetch ledger period_ids for all charges at the start of the transaction:
```php
// Before the charges loop, fetch period_ids from ledger
$chargePeriodMap = CustomerLedger::where('source_type', 'CHARGE')
    ->whereIn('source_id', $charges->pluck('charge_id'))
    ->pluck('period_id', 'source_id');

// In the loop:
$allocation = PaymentAllocation::create([
    // ...
    'period_id' => $chargePeriodMap->get($charge->charge_id),
    // ...
]);
```

**Verification:** After processing a payment, check PaymentAllocation records — charge allocations should have `period_id` set (matching the bill's period).

---

### Task 4: Update process-water-bill.blade.php — Group Charges Under Bills

**File:** `resources/views/pages/payment/process-water-bill.blade.php`

**Current layout:**
```
[Water Bills Section]
  - Bill January: ₱200
  - Bill February: ₱250

[Other Charges Section]
  - Late Payment Penalty - January (Bill #1): ₱10
  - Late Payment Penalty - February (Bill #2): ₱10
```

**New layout — charges grouped under their bill:**
```
[Water Bills + Associated Charges]
  Bill January: ₱200
    └ Late Payment Penalty - January: ₱10

  Bill February: ₱250
    └ Late Payment Penalty - February: ₱10

[Other Charges (no bill association)]   ← only if any have null period_id
  - Misc charge: ₱50
```

**Implementation:**
- In the Alpine.js component, compute a `billsWithCharges` getter that merges charges into their associated bills using `period_id`:
```javascript
get billsWithCharges() {
    return this.data.bills.map(bill => ({
        ...bill,
        charges: this.data.charges.filter(c => c.period_id === bill.period_id)
    }));
},

get unassociatedCharges() {
    const billPeriodIds = this.data.bills.map(b => b.period_id);
    return this.data.charges.filter(c => !c.period_id || !billPeriodIds.includes(c.period_id));
}
```

- Update the HTML template to render charges nested under each bill row
- Keep totals unchanged (full payment still covers everything)

**Verification:** Payment page shows charges grouped under their respective bills.

---

## Testing Checklist

- [ ] Payment queue: connection with 1 bill + 1 penalty → penalty shows on that bill row only
- [ ] Payment queue: connection with 2 bills + 1 penalty → penalty shows only on the correct bill row
- [ ] Payment queue: connection with 2 bills + 2 penalties → each penalty on its correct bill row
- [ ] Payment queue: connection with bills but NO penalties → no charges badge shown
- [ ] Process payment page: charges grouped under correct bills
- [ ] Process payment page: total amount still correct (bills + all charges)
- [ ] Payment processing: allocations have correct period_id
- [ ] Payment processing: ledger CREDIT entries have correct period_id
- [ ] Edge case: charge with null period_id in ledger → still appears (fallback handling)
- [ ] No regressions on application fee payment flow

## Files Modified

| File | Change |
|------|--------|
| `app/Services/Payment/PaymentManagementService.php` | JOIN ledger for charge grouping by period |
| `app/Services/Payment/PaymentService.php` | Associate charges with bills via ledger period_id |
| `resources/views/pages/payment/process-water-bill.blade.php` | Group charges under bills in UI |

## Dependencies

- **Blocked by:** PenaltyService developer setting `period_id` on CHARGE ledger entries
- **Can start independently:** Tasks 1-3 (code changes work with null fallback). Task 4 (UI) can use period_id when available.
- **Backfill:** Existing CHARGE ledger entries need `period_id` backfilled (other developer's scope)
