<?php

namespace App\Services\Billing;

use App\Models\CustomerLedger;
use App\Models\MeterAssignment;
use App\Models\MeterReading;
use App\Models\Period;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\WaterBillHistory;
use App\Models\WaterRate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WaterBillService
{
    /**
     * Get connections with active meter assignments for bill generation.
     */
    public function getBillableConnections(): Collection
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        return ServiceConnection::with([
            'customer',
            'accountType',
            'address.barangay',
            'meterAssignments' => function ($query) {
                $query->whereNull('removed_at');
            },
            'meterAssignments.meter',
        ])
            ->where('stat_id', $activeStatusId)
            ->whereNull('ended_at')
            ->whereHas('meterAssignments', function ($query) {
                $query->whereNull('removed_at');
            })
            ->orderBy('account_no')
            ->get()
            ->map(function ($connection) {
                $customer = $connection->customer;
                $customerName = $customer
                    ? trim($customer->cust_first_name.' '.$customer->cust_last_name)
                    : 'Unknown';

                $activeAssignment = $connection->meterAssignments->first();

                return [
                    'connection_id' => $connection->connection_id,
                    'account_no' => $connection->account_no,
                    'customer_name' => $customerName,
                    'account_type_id' => $connection->account_type_id,
                    'account_type' => $connection->accountType?->at_desc ?? 'Unknown',
                    'barangay' => $connection->address?->barangay?->b_name ?? 'Unknown',
                    'assignment_id' => $activeAssignment?->assignment_id,
                    'meter_serial' => $activeAssignment?->meter?->mtr_serial ?? 'N/A',
                    'install_read' => $activeAssignment?->install_read ?? 0,
                    'label' => $connection->account_no.' - '.$customerName,
                ];
            });
    }

    /**
     * Get available billing periods.
     */
    public function getBillingPeriods(): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $periods = Period::select('per_id', 'per_name', 'start_date', 'is_closed', 'stat_id')
            ->where('is_closed', false)
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($period) use ($activeStatusId) {
                return [
                    'per_id' => $period->per_id,
                    'per_name' => $period->per_name,
                    'start_date' => $period->start_date,
                    'is_active' => $period->stat_id === $activeStatusId,
                ];
            });

        // Find current active period
        $activePeriod = $periods->first(fn ($p) => $p['is_active']);
        $activePeriodId = $activePeriod ? $activePeriod['per_id'] : ($periods->first()['per_id'] ?? null);

        return [
            'periods' => $periods,
            'activePeriodId' => $activePeriodId,
        ];
    }

    /**
     * Get the last reading for a connection.
     */
    public function getLastReading(int $connectionId): ?array
    {
        $connection = ServiceConnection::with([
            'meterAssignments' => function ($query) {
                $query->whereNull('removed_at');
            },
        ])->find($connectionId);

        if (! $connection) {
            return null;
        }

        $assignment = $connection->meterAssignments->first();
        if (! $assignment) {
            return null;
        }

        // Get the last meter reading for this assignment
        $lastReading = MeterReading::where('assignment_id', $assignment->assignment_id)
            ->orderBy('reading_date', 'desc')
            ->orderBy('reading_id', 'desc')
            ->first();

        if ($lastReading) {
            return [
                'reading_id' => $lastReading->reading_id,
                'reading_value' => (float) $lastReading->reading_value,
                'reading_date' => $lastReading->reading_date?->format('Y-m-d'),
                'period_id' => $lastReading->period_id,
            ];
        }

        // Fall back to install reading
        return [
            'reading_id' => null,
            'reading_value' => (float) $assignment->install_read,
            'reading_date' => $assignment->installed_at?->format('Y-m-d'),
            'period_id' => null,
        ];
    }

    /**
     * Calculate bill amount based on consumption and account type.
     */
    public function calculateBillAmount(float $consumption, int $accountTypeId, ?int $periodId = null): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Get rates for the account type (period-specific or default)
        $rates = WaterRate::where('class_id', $accountTypeId)
            ->where('stat_id', $activeStatusId)
            ->when($periodId, function ($query) use ($periodId) {
                $query->where('period_id', $periodId);
            }, function ($query) {
                $query->whereNull('period_id');
            })
            ->orderBy('range_id')
            ->get();

        // If no period-specific rates, fall back to default
        if ($rates->isEmpty() && $periodId) {
            $rates = WaterRate::where('class_id', $accountTypeId)
                ->where('stat_id', $activeStatusId)
                ->whereNull('period_id')
                ->orderBy('range_id')
                ->get();
        }

        if ($rates->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No water rates found for this account type.',
                'amount' => 0,
                'breakdown' => [],
            ];
        }

        // Find the applicable rate tier
        $applicableRate = null;
        foreach ($rates as $rate) {
            if ($consumption >= $rate->range_min && $consumption <= $rate->range_max) {
                $applicableRate = $rate;
                break;
            }
        }

        // If consumption exceeds all ranges, use the highest tier
        if (! $applicableRate) {
            $applicableRate = $rates->last();
        }

        // Calculate amount
        $baseAmount = (float) $applicableRate->rate_val;
        $rateIncrement = (float) $applicableRate->rate_inc;
        $rangeMin = $applicableRate->range_min;
        $excessConsumption = 0;
        $excessAmount = 0;

        // If rate_inc > 0, calculate excess
        if ($rateIncrement > 0 && $consumption > $rangeMin) {
            $excessConsumption = $consumption - $rangeMin;
            $excessAmount = $excessConsumption * $rateIncrement;
        }

        $totalAmount = $baseAmount + $excessAmount;

        return [
            'success' => true,
            'amount' => round($totalAmount, 2),
            'breakdown' => [
                'consumption' => $consumption,
                'range' => $applicableRate->range_min.'-'.$applicableRate->range_max.' cu.m',
                'range_id' => $applicableRate->range_id,
                'base_amount' => $baseAmount,
                'rate_increment' => $rateIncrement,
                'excess_consumption' => $excessConsumption,
                'excess_amount' => round($excessAmount, 2),
                'total_amount' => round($totalAmount, 2),
            ],
        ];
    }

    /**
     * Generate a water bill.
     */
    public function generateBill(array $data): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Validate connection
        $connection = ServiceConnection::with([
            'accountType',
            'meterAssignments' => function ($query) {
                $query->whereNull('removed_at');
            },
        ])->find($data['connection_id']);

        if (! $connection) {
            return ['success' => false, 'message' => 'Service connection not found.'];
        }

        $assignment = $connection->meterAssignments->first();
        if (! $assignment) {
            return ['success' => false, 'message' => 'No active meter assignment for this connection.'];
        }

        // Validate period
        $period = Period::find($data['period_id']);
        if (! $period) {
            return ['success' => false, 'message' => 'Billing period not found.'];
        }

        if ($period->is_closed) {
            return ['success' => false, 'message' => 'Cannot generate bill for a closed period.'];
        }

        // Check if bill already exists for this connection and period
        $existingBill = WaterBillHistory::where('connection_id', $data['connection_id'])
            ->where('period_id', $data['period_id'])
            ->first();

        if ($existingBill) {
            return ['success' => false, 'message' => 'A bill already exists for this connection and period.'];
        }

        // Validate readings
        $prevReading = (float) $data['prev_reading'];
        $currReading = (float) $data['curr_reading'];

        if ($currReading < $prevReading) {
            return ['success' => false, 'message' => 'Current reading cannot be less than previous reading.'];
        }

        $consumption = $currReading - $prevReading;

        // Calculate bill amount
        $calculation = $this->calculateBillAmount(
            $consumption,
            $connection->account_type_id,
            $data['period_id']
        );

        if (! $calculation['success']) {
            return $calculation;
        }

        try {
            DB::beginTransaction();

            // Create previous reading record (if not existing)
            $prevReadingRecord = MeterReading::create([
                'assignment_id' => $assignment->assignment_id,
                'period_id' => $data['period_id'],
                'reading_date' => $data['reading_date'] ?? now()->format('Y-m-d'),
                'reading_value' => $prevReading,
                'is_estimated' => false,
                'meter_reader_id' => $data['meter_reader_id'] ?? 1, // Default to 1 if not provided
                'created_at' => now(),
            ]);

            // Create current reading record
            $currReadingRecord = MeterReading::create([
                'assignment_id' => $assignment->assignment_id,
                'period_id' => $data['period_id'],
                'reading_date' => $data['reading_date'] ?? now()->format('Y-m-d'),
                'reading_value' => $currReading,
                'is_estimated' => false,
                'meter_reader_id' => $data['meter_reader_id'] ?? 1,
                'created_at' => now(),
            ]);

            // Create water bill history
            $bill = WaterBillHistory::create([
                'connection_id' => $data['connection_id'],
                'period_id' => $data['period_id'],
                'prev_reading_id' => $prevReadingRecord->reading_id,
                'curr_reading_id' => $currReadingRecord->reading_id,
                'consumption' => $consumption,
                'water_amount' => $calculation['amount'],
                'due_date' => $data['due_date'] ?? now()->addDays(15)->format('Y-m-d'),
                'adjustment_total' => 0,
                'stat_id' => $activeStatusId,
            ]);

            // Create CustomerLedger debit entry for the bill
            CustomerLedger::create([
                'customer_id' => $connection->customer_id,
                'connection_id' => $data['connection_id'],
                'period_id' => $data['period_id'],
                'txn_date' => now()->format('Y-m-d'),
                'post_ts' => now(),
                'source_type' => 'BILL',
                'source_id' => $bill->bill_id,
                'source_line_no' => 1,
                'description' => 'Water Bill - '.$period->per_name.' (Consumption: '.$consumption.' cu.m)',
                'debit' => $calculation['amount'],
                'credit' => 0,
                'user_id' => Auth::id() ?? 1,
                'stat_id' => $activeStatusId,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Bill generated successfully.',
                'data' => [
                    'bill_id' => $bill->bill_id,
                    'connection_id' => $connection->connection_id,
                    'account_no' => $connection->account_no,
                    'period' => $period->per_name,
                    'consumption' => $consumption,
                    'amount' => $calculation['amount'],
                    'breakdown' => $calculation['breakdown'],
                    'due_date' => $bill->due_date,
                ],
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Failed to generate bill: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Preview bill calculation without saving.
     */
    public function previewBill(array $data): array
    {
        // Validate connection
        $connection = ServiceConnection::with(['accountType'])->find($data['connection_id']);

        if (! $connection) {
            return ['success' => false, 'message' => 'Service connection not found.'];
        }

        // Validate readings
        $prevReading = (float) ($data['prev_reading'] ?? 0);
        $currReading = (float) ($data['curr_reading'] ?? 0);

        if ($currReading < $prevReading) {
            return ['success' => false, 'message' => 'Current reading cannot be less than previous reading.'];
        }

        $consumption = $currReading - $prevReading;

        // Calculate bill amount
        $calculation = $this->calculateBillAmount(
            $consumption,
            $connection->account_type_id,
            $data['period_id'] ?? null
        );

        return $calculation;
    }

    /**
     * Get billing details for a specific connection.
     */
    public function getConnectionBillingDetails(int $connectionId): ?array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $paidStatusId = Status::getIdByDescription(Status::PAID) ?? Status::getIdByDescription('Paid') ?? 2;
        $overdueStatusId = Status::getIdByDescription(Status::OVERDUE) ?? Status::getIdByDescription('Overdue') ?? 4;

        $connection = ServiceConnection::with([
            'customer',
            'accountType',
            'address.barangay',
            'meterAssignments' => function ($query) {
                $query->whereNull('removed_at');
            },
            'meterAssignments.meter',
        ])->find($connectionId);

        if (! $connection) {
            return null;
        }

        $customer = $connection->customer;
        $customerName = $customer
            ? trim($customer->cust_first_name.' '.$customer->cust_last_name)
            : 'Unknown';

        $customerInitials = $customer
            ? strtoupper(substr($customer->cust_first_name, 0, 1).substr($customer->cust_last_name, 0, 1))
            : '--';

        $activeAssignment = $connection->meterAssignments->first();

        // Get all bills for this connection
        $bills = WaterBillHistory::with(['period', 'previousReading', 'currentReading'])
            ->where('connection_id', $connectionId)
            ->orderBy('due_date', 'desc')
            ->get();

        // Get unpaid bills (active or overdue)
        $unpaidBills = $bills->filter(function ($bill) use ($activeStatusId, $overdueStatusId) {
            return $bill->stat_id === $activeStatusId || $bill->stat_id === $overdueStatusId;
        });

        // Calculate overdue days if any overdue bills exist
        $overdueDays = 0;
        $oldestOverdueBill = $unpaidBills->first(fn ($b) => $b->stat_id === $overdueStatusId);
        if ($oldestOverdueBill && $oldestOverdueBill->due_date) {
            $overdueDays = max(0, now()->diffInDays($oldestOverdueBill->due_date, false));
        }

        // Current bill (most recent)
        $currentBill = $bills->first();

        // Total unpaid amount
        $totalUnpaidAmount = $unpaidBills->sum(function ($bill) {
            return (float) $bill->water_amount + (float) $bill->adjustment_total;
        });

        // Determine overall billing status
        $overallStatus = 'Current';
        if ($unpaidBills->count() > 0) {
            $overallStatus = $unpaidBills->some(fn ($b) => $b->stat_id === $overdueStatusId) ? 'Overdue' : 'Pending';
        }

        // Build billing history
        $billingHistory = $bills->map(function ($bill) use ($paidStatusId, $overdueStatusId) {
            $totalAmount = (float) $bill->water_amount + (float) $bill->adjustment_total;
            $status = 'UNPAID';
            if ($bill->stat_id === $paidStatusId) {
                $status = 'PAID';
            } elseif ($bill->stat_id === $overdueStatusId) {
                $status = 'OVERDUE';
            }

            return [
                'bill_id' => $bill->bill_id,
                'period' => $bill->period?->per_name ?? 'Unknown',
                'due_date' => $bill->due_date?->format('Y-m-d'),
                'consumption' => (float) $bill->consumption,
                'water_amount' => (float) $bill->water_amount,
                'adjustment_total' => (float) $bill->adjustment_total,
                'total_amount' => round($totalAmount, 2),
                'status' => $status,
            ];
        });

        // Build monthly trend data (last 12 months)
        $monthlyTrend = ['labels' => [], 'data' => []];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->format('M');
            $monthBill = $bills->first(function ($b) use ($date) {
                return $b->due_date && $b->due_date->format('Y-m') === $date->format('Y-m');
            });
            $monthlyTrend['labels'][] = $monthLabel;
            $monthlyTrend['data'][] = $monthBill ? round((float) $monthBill->water_amount + (float) $monthBill->adjustment_total, 2) : 0;
        }

        return [
            'connection_id' => $connection->connection_id,
            'account_no' => $connection->account_no,
            'customer_name' => $customerName,
            'customer_initials' => $customerInitials,
            'account_type' => $connection->accountType?->at_desc ?? 'Unknown',
            'meter_serial' => $activeAssignment?->meter?->mtr_serial ?? 'N/A',
            'barangay' => $connection->address?->barangay?->b_name ?? 'Unknown',
            'status' => $connection->stat_id === $activeStatusId ? 'Active' : 'Inactive',
            'email' => $customer?->cust_email ?? 'N/A',
            'phone' => $customer?->cust_phone ?? 'N/A',
            'overdue_days' => $overdueDays,
            'current_bill' => $currentBill ? [
                'bill_id' => $currentBill->bill_id,
                'period' => $currentBill->period?->per_name ?? 'N/A',
                'bill_date' => $currentBill->created_at?->format('Y-m-d'),
                'due_date' => $currentBill->due_date?->format('Y-m-d'),
                'consumption' => (float) $currentBill->consumption,
                'water_amount' => (float) $currentBill->water_amount,
                'adjustment_total' => (float) $currentBill->adjustment_total,
                'total_amount' => round((float) $currentBill->water_amount + (float) $currentBill->adjustment_total, 2),
                'prev_reading' => $currentBill->previousReading?->reading_value ?? 0,
                'curr_reading' => $currentBill->currentReading?->reading_value ?? 0,
                'reading_date' => $currentBill->currentReading?->reading_date?->format('Y-m-d'),
                'status' => $currentBill->stat_id === $paidStatusId ? 'PAID' : ($currentBill->stat_id === $overdueStatusId ? 'OVERDUE' : 'UNPAID'),
            ] : null,
            'total_bills' => $bills->count(),
            'unpaid_bills' => $unpaidBills->count(),
            'total_unpaid_amount' => round($totalUnpaidAmount, 2),
            'overall_status' => $overallStatus,
            'billing_history' => $billingHistory->values()->toArray(),
            'monthly_trend' => $monthlyTrend,
        ];
    }

    /**
     * Get all connections with billing summary for consumer billing list.
     */
    public function getConsumerBillingList(): Collection
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $paidStatusId = Status::getIdByDescription(Status::PAID) ?? Status::getIdByDescription('Paid') ?? 2;
        $overdueStatusId = Status::getIdByDescription(Status::OVERDUE) ?? Status::getIdByDescription('Overdue') ?? 4;

        return ServiceConnection::with([
            'customer',
            'accountType',
            'address.barangay',
            'meterAssignments' => function ($query) {
                $query->whereNull('removed_at');
            },
            'meterAssignments.meter',
        ])
            ->where('stat_id', $activeStatusId)
            ->whereNull('ended_at')
            ->orderBy('account_no')
            ->get()
            ->map(function ($connection) use ($activeStatusId, $paidStatusId, $overdueStatusId) {
                $customer = $connection->customer;
                $customerName = $customer
                    ? trim($customer->cust_first_name.' '.$customer->cust_last_name)
                    : 'Unknown';

                $activeAssignment = $connection->meterAssignments->first();

                // Get unpaid bills
                $unpaidBills = WaterBillHistory::where('connection_id', $connection->connection_id)
                    ->whereIn('stat_id', [$activeStatusId, $overdueStatusId])
                    ->get();

                $totalDue = $unpaidBills->sum(function ($bill) {
                    return (float) $bill->water_amount + (float) $bill->adjustment_total;
                });

                $unpaidCount = $unpaidBills->count();
                $hasOverdue = $unpaidBills->contains(fn ($b) => $b->stat_id === $overdueStatusId);

                // Determine status
                $status = 'Current';
                if ($hasOverdue) {
                    $status = 'Overdue';
                } elseif ($unpaidCount > 0) {
                    $status = 'Pending';
                }

                return [
                    'connection_id' => $connection->connection_id,
                    'account_no' => $connection->account_no,
                    'name' => $customerName,
                    'location' => $connection->address?->barangay?->b_name ?? 'Unknown',
                    'meter_serial' => $activeAssignment?->meter?->mtr_serial ?? 'N/A',
                    'account_type' => $connection->accountType?->at_desc ?? 'Unknown',
                    'totalDue' => round($totalDue, 2),
                    'unpaidCount' => $unpaidCount,
                    'status' => $status,
                ];
            });
    }

    /**
     * Get billed consumers for a specific period with statistics.
     */
    public function getBilledConsumersByPeriod(?int $periodId = null): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $paidStatusId = Status::getIdByDescription(Status::PAID) ?? Status::getIdByDescription('Paid') ?? 2;
        $overdueStatusId = Status::getIdByDescription(Status::OVERDUE) ?? Status::getIdByDescription('Overdue') ?? 4;

        // If no period specified, get the active period
        if (! $periodId) {
            $activePeriod = Period::where('stat_id', $activeStatusId)
                ->where('is_closed', false)
                ->first();
            $periodId = $activePeriod?->per_id;
        }

        // Get all bills for the period
        $billsQuery = WaterBillHistory::with([
            'serviceConnection.customer',
            'serviceConnection.accountType',
            'serviceConnection.address.barangay',
            'serviceConnection.meterAssignments' => function ($query) {
                $query->whereNull('removed_at');
            },
            'serviceConnection.meterAssignments.meter',
            'period',
        ]);

        if ($periodId) {
            $billsQuery->where('period_id', $periodId);
        }

        $bills = $billsQuery->orderBy('due_date', 'desc')->get();

        // Calculate statistics
        $totalBilled = $bills->sum(function ($bill) {
            return (float) $bill->water_amount + (float) $bill->adjustment_total;
        });

        $totalPaid = $bills->where('stat_id', $paidStatusId)->sum(function ($bill) {
            return (float) $bill->water_amount + (float) $bill->adjustment_total;
        });

        $totalOutstanding = $bills->whereIn('stat_id', [$activeStatusId, $overdueStatusId])->sum(function ($bill) {
            return (float) $bill->water_amount + (float) $bill->adjustment_total;
        });

        $totalAdjustments = $bills->sum(function ($bill) {
            return (float) $bill->adjustment_total;
        });

        $overdueBillsCount = $bills->where('stat_id', $overdueStatusId)->count();
        $paidBillsCount = $bills->where('stat_id', $paidStatusId)->count();
        $unpaidBillsCount = $bills->whereIn('stat_id', [$activeStatusId, $overdueStatusId])->count();

        // Map bills to consumer data
        $consumers = $bills->map(function ($bill) use ($activeStatusId, $paidStatusId, $overdueStatusId) {
            $connection = $bill->serviceConnection;
            if (! $connection) {
                return null;
            }

            $customer = $connection->customer;
            $customerName = $customer
                ? trim($customer->cust_first_name.' '.$customer->cust_last_name)
                : 'Unknown';

            $activeAssignment = $connection->meterAssignments->first();
            $totalAmount = (float) $bill->water_amount + (float) $bill->adjustment_total;

            // Determine status
            $status = 'Paid';
            if ($bill->stat_id === $overdueStatusId) {
                $status = 'Overdue';
            } elseif ($bill->stat_id === $activeStatusId) {
                $status = 'Pending';
            }

            return [
                'connection_id' => $connection->connection_id,
                'bill_id' => $bill->bill_id,
                'account_no' => $connection->account_no,
                'name' => $customerName,
                'location' => $connection->address?->barangay?->b_name ?? 'Unknown',
                'meter_serial' => $activeAssignment?->meter?->mtr_serial ?? 'N/A',
                'account_type' => $connection->accountType?->at_desc ?? 'Unknown',
                'period' => $bill->period?->per_name ?? 'N/A',
                'consumption' => (float) $bill->consumption,
                'water_amount' => (float) $bill->water_amount,
                'adjustment_total' => (float) $bill->adjustment_total,
                'totalDue' => round($totalAmount, 2),
                'due_date' => $bill->due_date?->format('Y-m-d'),
                'status' => $status,
            ];
        })->filter()->values();

        return [
            'consumers' => $consumers->toArray(),
            'statistics' => [
                'total_billed' => round($totalBilled, 2),
                'total_paid' => round($totalPaid, 2),
                'total_outstanding' => round($totalOutstanding, 2),
                'total_adjustments' => round($totalAdjustments, 2),
                'total_bills' => $bills->count(),
                'paid_bills' => $paidBillsCount,
                'unpaid_bills' => $unpaidBillsCount,
                'overdue_bills' => $overdueBillsCount,
            ],
            'period_id' => $periodId,
        ];
    }

    /**
     * Get billing summary statistics (for summary cards).
     */
    public function getBillingSummary(): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $paidStatusId = Status::getIdByDescription(Status::PAID) ?? Status::getIdByDescription('Paid') ?? 2;
        $overdueStatusId = Status::getIdByDescription(Status::OVERDUE) ?? Status::getIdByDescription('Overdue') ?? 4;

        // Get all unpaid bills
        $unpaidBills = WaterBillHistory::whereIn('stat_id', [$activeStatusId, $overdueStatusId])->get();

        $totalOutstanding = $unpaidBills->sum(function ($bill) {
            return (float) $bill->water_amount + (float) $bill->adjustment_total;
        });

        $overdueBillsCount = $unpaidBills->where('stat_id', $overdueStatusId)->count();

        // Get total paid (all time or current month)
        $totalPaid = WaterBillHistory::where('stat_id', $paidStatusId)
            ->sum(DB::raw('water_amount + adjustment_total'));

        // Get total adjustments
        $totalAdjustments = WaterBillHistory::sum('adjustment_total');

        return [
            'outstanding_balance' => round((float) $totalOutstanding, 2),
            'total_paid' => round((float) $totalPaid, 2),
            'overdue_bills' => $overdueBillsCount,
            'total_adjustments' => round((float) abs($totalAdjustments), 2),
        ];
    }
}
