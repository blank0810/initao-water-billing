<?php

namespace App\Services\Billing;

use App\Models\CustomerLedger;
use App\Models\MeterAssignment;
use App\Models\MeterReading;
use App\Models\Period;
use App\Models\ReadingSchedule;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\UploadedReading;
use App\Models\WaterBillHistory;
use App\Models\WaterRate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WaterBillService
{
    /**
     * Get connections with active meter assignments for bill generation.
     */
    public function getBillableConnections(string $search = '', int $limit = 100): Collection
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $query = ServiceConnection::with([
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
            });

        // Apply search filter
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('account_no', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('cust_first_name', 'like', "%{$search}%")
                            ->orWhere('cust_last_name', 'like', "%{$search}%")
                            ->orWhereRaw("CONCAT(cust_first_name, ' ', cust_last_name) LIKE ?", ["%{$search}%"]);
                    })
                    ->orWhereHas('address.barangay', function ($addressQuery) use ($search) {
                        $addressQuery->where('b_desc', 'like', "%{$search}%");
                    })
                    ->orWhereHas('meterAssignments.meter', function ($meterQuery) use ($search) {
                        $meterQuery->where('mtr_serial', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('account_no')
            ->limit($limit)
            ->get()
            ->map(function ($connection) {
                $customer = $connection->customer;
                $customerName = $customer
                    ? trim($customer->cust_first_name.' '.$customer->cust_last_name)
                    : 'Unknown';

                $activeAssignment = $connection->meterAssignments->first();

                // Get meter change data if applicable
                $changeMeter = $connection->change_meter ?? false;
                $removalRead = null;
                if ($changeMeter) {
                    $prevAssignment = MeterAssignment::where('connection_id', $connection->connection_id)
                        ->whereNotNull('removed_at')
                        ->whereNotNull('removal_read')
                        ->latest('removed_at')
                        ->first();
                    $removalRead = $prevAssignment ? (float) $prevAssignment->removal_read : null;
                }

                return [
                    'connection_id' => $connection->connection_id,
                    'account_no' => $connection->account_no,
                    'customer_name' => $customerName,
                    'account_type_id' => $connection->account_type_id,
                    'account_type' => $connection->accountType?->at_desc ?? 'Unknown',
                    'barangay' => $connection->address?->barangay?->b_desc ?? 'Unknown',
                    'assignment_id' => $activeAssignment?->assignment_id,
                    'meter_serial' => $activeAssignment?->meter?->mtr_serial ?? 'N/A',
                    'install_read' => $activeAssignment?->install_read ?? 0,
                    'label' => $connection->account_no.' - '.$customerName,
                    'change_meter' => $changeMeter,
                    'removal_read' => $removalRead,
                ];
            });
    }

    /**
     * Get available billing periods.
     */
    public function getBillingPeriods(): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $periods = Period::select('per_id', 'per_name', 'start_date', 'end_date', 'grace_period', 'is_closed', 'stat_id')
            ->where('is_closed', false)
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($period) use ($activeStatusId) {
                return [
                    'per_id' => $period->per_id,
                    'per_name' => $period->per_name,
                    'start_date' => $period->start_date,
                    'end_date' => $period->end_date,
                    'grace_period' => $period->grace_period,
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

        $result = $lastReading
            ? [
                'reading_id' => $lastReading->reading_id,
                'reading_value' => (float) $lastReading->reading_value,
                'reading_date' => $lastReading->reading_date?->format('Y-m-d'),
                'period_id' => $lastReading->period_id,
            ]
            : [
                'reading_id' => null,
                'reading_value' => (float) $assignment->install_read,
                'reading_date' => $assignment->installed_at?->format('Y-m-d'),
                'period_id' => null,
            ];

        // Add meter change context
        $result['change_meter'] = $connection->change_meter ?? false;
        if ($result['change_meter']) {
            $meterChangeData = $this->getMeterChangeData($connectionId);
            $result['old_meter_consumption'] = $meterChangeData['old_meter_consumption'] ?? 0;
            $result['removal_read'] = $meterChangeData['removal_read'] ?? null;
            $result['old_meter_previous_reading'] = $meterChangeData['old_meter_previous_reading'] ?? null;
        }

        return $result;
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

        // Calculate amount: consumption × rate per cu.m.
        $ratePerCum = (float) $applicableRate->rate_val;
        $totalAmount = $consumption * $ratePerCum;

        return [
            'success' => true,
            'amount' => round($totalAmount, 2),
            'breakdown' => [
                'consumption' => $consumption,
                'range' => $applicableRate->range_min.'-'.$applicableRate->range_max.' cu.m',
                'range_id' => $applicableRate->range_id,
                'rate_per_cum' => $ratePerCum,
                'total_amount' => round($totalAmount, 2),
            ],
        ];
    }

    /**
     * Get meter change data for a connection if change_meter flag is set.
     *
     * @return array|null Meter change data or null if not a meter change
     */
    private function getMeterChangeData(int $connectionId): ?array
    {
        $connection = ServiceConnection::find($connectionId);

        // ============================================
        // NORMAL BILLING (change_meter = false)
        // ============================================
        if (! $connection || ! $connection->change_meter) {
            return null;
        }

        // ============================================
        // METER CHANGE BILLING (change_meter = true)
        // ============================================
        // Get the previously removed meter assignment
        $previousAssignment = MeterAssignment::where('connection_id', $connectionId)
            ->whereNotNull('removed_at')
            ->whereNotNull('removal_read')
            ->latest('removed_at')
            ->first();

        if (! $previousAssignment) {
            return null;
        }

        // Get the previous reading for the old meter (last billed or install_read)
        $previousReading = $this->getLastReadingForAssignment($previousAssignment->assignment_id);

        return [
            'old_assignment_id' => $previousAssignment->assignment_id,
            'removal_read' => (float) $previousAssignment->removal_read,
            'old_meter_previous_reading' => $previousReading,
            'old_meter_consumption' => max(0, (float) $previousAssignment->removal_read - $previousReading),
        ];
    }

    /**
     * Get the last reading value for a specific assignment.
     */
    private function getLastReadingForAssignment(int $assignmentId): float
    {
        $assignment = MeterAssignment::find($assignmentId);

        if (! $assignment) {
            return 0;
        }

        // Try to get the last billed reading
        $lastReading = MeterReading::where('assignment_id', $assignmentId)
            ->whereNotNull('period_id')
            ->orderBy('reading_date', 'desc')
            ->orderBy('reading_id', 'desc')
            ->first();

        if ($lastReading) {
            return (float) $lastReading->reading_value;
        }

        // Fall back to install_read
        return (float) $assignment->install_read;
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

        // ============================================
        // METER CHANGE HANDLING (change_meter = true)
        // ============================================
        $isMeterChange = false;
        $meterChangeData = null;
        $oldMeterConsumption = null;
        $newMeterConsumption = null;

        if ($connection->change_meter) {
            $meterChangeData = $this->getMeterChangeData($data['connection_id']);

            if (! $meterChangeData) {
                return [
                    'success' => false,
                    'message' => 'Meter change flag is set but no removed meter assignment found. Please verify meter change setup.',
                ];
            }

            $isMeterChange = true;
            $oldMeterConsumption = $meterChangeData['old_meter_consumption'];
            $newMeterConsumption = $consumption; // New meter: curr_reading - prev_reading (install_read)
            $consumption = $oldMeterConsumption + $newMeterConsumption; // Combined total
        }
        // ============================================
        // NORMAL BILLING (change_meter = false)
        // ============================================
        // consumption = $currReading - $prevReading (already calculated above)

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
                'due_date' => $data['due_date'] ?? now()->addDays($period->grace_period)->format('Y-m-d'),
                'adjustment_total' => 0,
                'is_meter_change' => $isMeterChange,
                'old_assignment_id' => $meterChangeData['old_assignment_id'] ?? null,
                'old_meter_consumption' => $oldMeterConsumption,
                'new_meter_consumption' => $newMeterConsumption,
                'stat_id' => $activeStatusId,
            ]);

            // Create CustomerLedger debit entry for the bill
            $description = 'Water Bill - '.$period->per_name.' (Consumption: '.$consumption.' cu.m)';
            if ($isMeterChange) {
                $description .= ' [Meter Change: Old='.$oldMeterConsumption.', New='.$newMeterConsumption.']';
            }

            CustomerLedger::create([
                'customer_id' => $connection->customer_id,
                'connection_id' => $data['connection_id'],
                'period_id' => $data['period_id'],
                'txn_date' => now()->format('Y-m-d'),
                'post_ts' => now(),
                'source_type' => 'BILL',
                'source_id' => $bill->bill_id,
                'source_line_no' => 1,
                'description' => $description,
                'debit' => $calculation['amount'],
                'credit' => 0,
                'user_id' => Auth::id() ?? 1,
                'stat_id' => $activeStatusId,
            ]);

            // Clear change_meter flag after successful billing
            if ($isMeterChange) {
                ServiceConnection::where('connection_id', $data['connection_id'])
                    ->update(['change_meter' => false]);
            }

            DB::commit();

            $responseData = [
                'bill_id' => $bill->bill_id,
                'connection_id' => $connection->connection_id,
                'account_no' => $connection->account_no,
                'period' => $period->per_name,
                'consumption' => $consumption,
                'amount' => $calculation['amount'],
                'breakdown' => $calculation['breakdown'],
                'due_date' => $bill->due_date,
            ];

            if ($isMeterChange) {
                $responseData['meter_change'] = [
                    'is_meter_change' => true,
                    'old_meter_consumption' => $oldMeterConsumption,
                    'new_meter_consumption' => $newMeterConsumption,
                ];
            }

            return [
                'success' => true,
                'message' => 'Bill generated successfully.',
                'data' => $responseData,
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

        // ============================================
        // METER CHANGE HANDLING (change_meter = true)
        // ============================================
        $meterChangeData = null;
        $oldMeterConsumption = 0;
        $newMeterConsumption = $consumption;

        if ($connection->change_meter) {
            $meterChangeData = $this->getMeterChangeData($data['connection_id']);

            if ($meterChangeData) {
                $oldMeterConsumption = $meterChangeData['old_meter_consumption'];
                $newMeterConsumption = $consumption; // New meter: curr - prev (which is install_read)
                $consumption = $oldMeterConsumption + $newMeterConsumption; // Total consumption
            }
        }
        // ============================================
        // NORMAL BILLING (change_meter = false)
        // ============================================
        // consumption = $currReading - $prevReading (already calculated above)

        // Calculate bill amount based on total consumption
        $calculation = $this->calculateBillAmount(
            $consumption,
            $connection->account_type_id,
            $data['period_id'] ?? null
        );

        // Add meter change info to response if applicable
        if ($meterChangeData) {
            $calculation['meter_change'] = true;
            $calculation['old_meter_consumption'] = $oldMeterConsumption;
            $calculation['new_meter_consumption'] = $newMeterConsumption;
            $calculation['breakdown']['total_consumption'] = $consumption;
        }

        return $calculation;
    }

    /**
     * Get billing details for a specific connection.
     */
    public function getConnectionBillingDetails(int $connectionId): ?array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $paidStatusId = Status::getIdByDescription(Status::PAID) ?? Status::getIdByDescription('Paid') ?? 2;
        $overdueStatusId = Status::getIdByDescription('OVERDUE') ?? Status::getIdByDescription('Overdue') ?? 4;

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
                'photo_url' => $bill->photo_url,
                'has_photo' => $bill->has_photo,
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
            'barangay' => $connection->address?->barangay?->b_desc ?? 'Unknown',
            'status' => $connection->stat_id === $activeStatusId ? 'Active' : 'Inactive',
            'email' => 'N/A', // Email column not available in customer table
            'phone' => $customer?->contact_number ?? 'N/A',
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
        $overdueStatusId = Status::getIdByDescription('OVERDUE') ?? Status::getIdByDescription('Overdue') ?? 4;

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
            ->map(function ($connection) use ($activeStatusId, $overdueStatusId) {
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
                    'location' => $connection->address?->barangay?->b_desc ?? 'Unknown',
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
        $overdueStatusId = Status::getIdByDescription('OVERDUE') ?? Status::getIdByDescription('Overdue') ?? 4;

        // Get all bills (filtered by period if specified, otherwise all)
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
        $consumers = $bills->map(function ($bill) use ($activeStatusId, $overdueStatusId) {
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
                'location' => $connection->address?->barangay?->b_desc ?? 'Unknown',
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
        $overdueStatusId = Status::getIdByDescription('OVERDUE') ?? Status::getIdByDescription('Overdue') ?? 4;

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

    /**
     * Process uploaded readings into MeterReading and WaterBillHistory records.
     *
     * @param  array  $uploadedReadingIds  Array of uploaded_reading_id values to process
     * @return array Result with success status, processed count, and any errors
     */
    public function processUploadedReadings(array $uploadedReadingIds): array
    {
        // Ensure we get a valid Active status ID (fallback to 2 if lookup fails)
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE) ?? 2;
        $userId = Auth::id() ?? 1;

        $processed = 0;
        $skipped = 0;
        $errors = [];

        // Get uploaded readings that are not yet processed
        $uploadedReadings = UploadedReading::whereIn('uploaded_reading_id', $uploadedReadingIds)
            ->where('is_processed', false)
            ->whereNotNull('present_reading')
            ->whereNotNull('previous_reading')
            ->whereNotNull('connection_id')
            ->get();

        if ($uploadedReadings->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No valid unprocessed readings found.',
                'processed' => 0,
                'skipped' => count($uploadedReadingIds),
                'errors' => [],
            ];
        }

        foreach ($uploadedReadings as $uploadedReading) {
            try {
                $result = $this->processSingleUploadedReading($uploadedReading, $activeStatusId, $userId);

                if ($result['success']) {
                    $processed++;
                } else {
                    $skipped++;
                    $errors[] = [
                        'uploaded_reading_id' => $uploadedReading->uploaded_reading_id,
                        'account_no' => $uploadedReading->account_no,
                        'message' => $result['message'],
                    ];
                }
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = [
                    'uploaded_reading_id' => $uploadedReading->uploaded_reading_id,
                    'account_no' => $uploadedReading->account_no,
                    'message' => 'Processing error: '.$e->getMessage(),
                ];
                Log::error('Error processing uploaded reading', [
                    'uploaded_reading_id' => $uploadedReading->uploaded_reading_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'success' => $processed > 0,
            'message' => $processed > 0
                ? "Successfully processed {$processed} reading(s)."
                : 'No readings were processed.',
            'processed' => $processed,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Process a single uploaded reading into MeterReading and WaterBillHistory.
     */
    private function processSingleUploadedReading(UploadedReading $uploadedReading, int $activeStatusId, int $userId): array
    {
        // Get the service connection
        $connection = ServiceConnection::with([
            'accountType',
            'meterAssignments' => function ($query) {
                $query->whereNull('removed_at');
            },
        ])->find($uploadedReading->connection_id);

        if (! $connection) {
            return ['success' => false, 'message' => 'Service connection not found.'];
        }

        // Get the active meter assignment
        $assignment = $connection->meterAssignments->first();
        if (! $assignment) {
            return ['success' => false, 'message' => 'No active meter assignment found.'];
        }

        // Get the period from the reading schedule
        $schedule = ReadingSchedule::find($uploadedReading->schedule_id);
        if (! $schedule || ! $schedule->period_id) {
            return ['success' => false, 'message' => 'Reading schedule or period not found.'];
        }

        $periodId = $schedule->period_id;
        $period = Period::find($periodId);

        if (! $period) {
            return ['success' => false, 'message' => 'Billing period not found.'];
        }

        if ($period->is_closed) {
            return ['success' => false, 'message' => 'Cannot process for a closed period.'];
        }

        // Check if bill already exists for this connection and period
        $existingBill = WaterBillHistory::where('connection_id', $uploadedReading->connection_id)
            ->where('period_id', $periodId)
            ->first();

        if ($existingBill) {
            return ['success' => false, 'message' => 'Bill already exists for this connection and period.'];
        }

        // Validate readings
        $currReading = (float) $uploadedReading->present_reading;

        // ============================================
        // METER CHANGE HANDLING (is_meter_change = true)
        // ============================================
        $isMeterChange = (bool) $uploadedReading->is_meter_change;
        $meterChangeData = null;
        $oldMeterConsumption = null;
        $newMeterConsumption = null;

        if ($isMeterChange) {
            // Validate meter change data is complete
            if ($uploadedReading->install_read === null) {
                return ['success' => false, 'message' => 'Meter change flag is set but install reading is missing.'];
            }

            // New meter: previous reading is install_read (not previous_reading which is old meter's last billed)
            $prevReading = (float) $uploadedReading->install_read;

            // Validate before calculating - current reading must be >= install_read
            if ($currReading < $prevReading) {
                return ['success' => false, 'message' => 'Current reading cannot be less than install reading.'];
            }

            $newMeterConsumption = $currReading - $prevReading;
            $consumption = $newMeterConsumption;

            // Old meter consumption - required for meter change
            $meterChangeData = $this->getMeterChangeData($uploadedReading->connection_id);
            if (! $meterChangeData) {
                return [
                    'success' => false,
                    'message' => 'Meter change flag is set but no removed meter assignment found. Please verify meter change setup.',
                ];
            }

            $oldMeterConsumption = $meterChangeData['old_meter_consumption'];
            $consumption += $oldMeterConsumption; // Combined total
        } else {
            // ============================================
            // NORMAL BILLING (is_meter_change = false)
            // ============================================
            $prevReading = (float) $uploadedReading->previous_reading;

            // Validate before calculating
            if ($currReading < $prevReading) {
                return ['success' => false, 'message' => 'Current reading cannot be less than previous reading.'];
            }

            $consumption = $currReading - $prevReading;
        }

        // Calculate bill amount
        $calculation = $this->calculateBillAmount(
            $consumption,
            $connection->account_type_id,
            $periodId
        );

        if (! $calculation['success']) {
            return $calculation;
        }

        try {
            DB::beginTransaction();

            // Get the meter reader ID from the schedule
            $meterReaderId = $schedule->reader_id ?? 1;

            // Create previous reading record (install_read for meter change, previous_reading otherwise)
            $prevReadingRecord = MeterReading::create([
                'assignment_id' => $assignment->assignment_id,
                'period_id' => $periodId,
                'reading_date' => $uploadedReading->reading_date ?? now()->format('Y-m-d'),
                'reading_value' => $prevReading,
                'is_estimated' => false,
                'meter_reader_id' => $meterReaderId,
                'created_at' => now(),
            ]);

            // Create current reading record
            $currReadingRecord = MeterReading::create([
                'assignment_id' => $assignment->assignment_id,
                'period_id' => $periodId,
                'reading_date' => $uploadedReading->reading_date ?? now()->format('Y-m-d'),
                'reading_value' => $currReading,
                'is_estimated' => false,
                'meter_reader_id' => $meterReaderId,
                'created_at' => now(),
            ]);

            // Create water bill history (inherit photo_path from uploaded reading)
            $bill = WaterBillHistory::create([
                'connection_id' => $uploadedReading->connection_id,
                'period_id' => $periodId,
                'prev_reading_id' => $prevReadingRecord->reading_id,
                'curr_reading_id' => $currReadingRecord->reading_id,
                'consumption' => $consumption,
                'water_amount' => $calculation['amount'],
                'due_date' => now()->addDays($period->grace_period)->format('Y-m-d'),
                'adjustment_total' => 0,
                'is_meter_change' => $isMeterChange,
                'old_assignment_id' => $meterChangeData['old_assignment_id'] ?? null,
                'old_meter_consumption' => $oldMeterConsumption,
                'new_meter_consumption' => $newMeterConsumption,
                'stat_id' => $activeStatusId,
                'photo_path' => $uploadedReading->photo_path,
            ]);

            // Create CustomerLedger debit entry for the bill
            $description = 'Water Bill - '.$period->per_name.' (Consumption: '.$consumption.' cu.m)';
            if ($isMeterChange) {
                $description .= ' [Meter Change: Old='.$oldMeterConsumption.', New='.$newMeterConsumption.']';
            }

            CustomerLedger::create([
                'customer_id' => $connection->customer_id,
                'connection_id' => $uploadedReading->connection_id,
                'period_id' => $periodId,
                'txn_date' => now()->format('Y-m-d'),
                'post_ts' => now(),
                'source_type' => 'BILL',
                'source_id' => $bill->bill_id,
                'source_line_no' => 1,
                'description' => $description,
                'debit' => $calculation['amount'],
                'credit' => 0,
                'user_id' => $userId,
                'stat_id' => $activeStatusId,
            ]);

            // Clear change_meter flag after successful billing
            if ($isMeterChange) {
                ServiceConnection::where('connection_id', $uploadedReading->connection_id)
                    ->update(['change_meter' => false]);
            }

            // Update the uploaded reading as processed
            $uploadedReading->update([
                'is_processed' => true,
                'processed_at' => now(),
                'processed_by' => $userId,
                'bill_id' => $bill->bill_id,
                'computed_amount' => $calculation['amount'],
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Bill generated successfully.',
                'bill_id' => $bill->bill_id,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get all data needed to render a printable billing statement for a specific bill.
     */
    public function getBillPrintData(int $billId): ?array
    {
        $bill = WaterBillHistory::with([
            'serviceConnection.customer',
            'serviceConnection.accountType',
            'serviceConnection.area',
            'serviceConnection.address.purok',
            'serviceConnection.address.barangay',
            'serviceConnection.meterAssignments' => function ($query) {
                $query->whereNull('removed_at');
            },
            'serviceConnection.meterAssignments.meter',
            'period',
            'previousReading',
            'currentReading',
            'billAdjustments.billAdjustmentType',
            'oldMeterAssignment.meter',
        ])->find($billId);

        if (! $bill) {
            return null;
        }

        $connection = $bill->serviceConnection;
        if (! $connection) {
            return null;
        }

        $customer = $connection->customer;
        $activeAssignment = $connection->meterAssignments->first();

        // Customer name
        $customerName = $customer
            ? trim(implode(' ', array_filter([
                $customer->cust_first_name,
                $customer->cust_middle_name ? substr($customer->cust_middle_name, 0, 1).'.' : '',
                $customer->cust_last_name,
            ])))
            : 'Unknown';

        // Full address
        $purokDesc = $connection?->address?->purok?->p_desc ?? '';
        $barangayDesc = $connection?->address?->barangay?->b_desc ?? '';
        $fullAddress = trim(implode(', ', array_filter([$purokDesc, $barangayDesc, 'Initao, Misamis Oriental'])));

        // Outstanding balance for this connection (excluding current bill)
        // Uses CustomerLedger for accuracy with partial payments
        // Arrears = BILL debits - PAYMENT credits (excludes current bill's ledger entry)
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $totalBillDebits = CustomerLedger::where('connection_id', $connection->connection_id)
            ->where('source_type', 'BILL')
            ->where('source_id', '!=', $billId)
            ->where('stat_id', $activeStatusId)
            ->sum('debit');

        $totalPaymentCredits = CustomerLedger::where('connection_id', $connection->connection_id)
            ->where('source_type', 'PAYMENT')
            ->where('stat_id', $activeStatusId)
            ->sum('credit');

        $arrears = max(0, (float) $totalBillDebits - (float) $totalPaymentCredits);

        // Bill adjustments (include direction for proper display)
        $adjustments = $bill->billAdjustments
            ->filter(fn ($adj) => $adj->stat_id === $activeStatusId)
            ->map(function ($adj) {
                $direction = $adj->billAdjustmentType?->direction ?? 'debit';

                // For consumption adjustments, direction depends on bill change
                if ($adj->adjustment_category === 'consumption' && $adj->old_amount !== null && $adj->new_amount !== null) {
                    $direction = ((float) $adj->new_amount >= (float) $adj->old_amount) ? 'debit' : 'credit';
                }

                $amount = (float) $adj->amount;

                return [
                    'type' => $adj->billAdjustmentType?->name ?? 'Adjustment',
                    'amount' => $direction === 'credit' ? -$amount : $amount,
                ];
            });

        // Net adjustment amount from ledger ADJUST entries for this bill
        $adjustLedgerDebit = CustomerLedger::where('connection_id', $connection->connection_id)
            ->where('source_type', 'ADJUST')
            ->where('stat_id', $activeStatusId)
            ->whereIn('source_id', $bill->billAdjustments->pluck('bill_adjustment_id'))
            ->sum('debit');
        $adjustLedgerCredit = CustomerLedger::where('connection_id', $connection->connection_id)
            ->where('source_type', 'ADJUST')
            ->where('stat_id', $activeStatusId)
            ->whereIn('source_id', $bill->billAdjustments->pluck('bill_adjustment_id'))
            ->sum('credit');
        $netAdjustmentFromLedger = (float) $adjustLedgerDebit - (float) $adjustLedgerCredit;

        // Recent consumption history (last 6 billing periods for this connection)
        $recentBills = WaterBillHistory::with('period')
            ->where('connection_id', $connection->connection_id)
            ->orderBy('period_id', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        $consumptionHistory = $recentBills->map(function ($b) {
            return [
                'period' => $b->period?->per_name ?? '',
                'consumption' => (float) $b->consumption,
                'amount' => round((float) $b->water_amount + (float) $b->adjustment_total, 2),
            ];
        });

        // Total = base bill + stored adjustments + ledger-only adjustments (consumption corrections)
        $totalAmount = (float) $bill->water_amount + (float) $bill->adjustment_total + $netAdjustmentFromLedger;

        // Look up applicable rate for this bill
        $ratePerCum = null;
        $rateTierRange = null;
        $accountTypeId = $connection->account_type_id;
        $periodId = $bill->period_id;
        if ($accountTypeId) {
            $applicableRate = WaterRate::where('stat_id', $activeStatusId)
                ->where('class_id', $accountTypeId)
                ->where(function ($q) use ($periodId) {
                    $q->where('period_id', $periodId)
                        ->orWhereNull('period_id');
                })
                ->orderByDesc('period_id')
                ->where('range_min', '<=', $bill->consumption)
                ->where('range_max', '>=', $bill->consumption)
                ->first();

            if ($applicableRate) {
                $ratePerCum = (float) $applicableRate->rate_val;
                $rateTierRange = $applicableRate->range_min.'-'.$applicableRate->range_max;
            }
        }

        return [
            'bill' => [
                'bill_id' => $bill->bill_id,
                'consumption' => (float) $bill->consumption,
                'water_amount' => (float) $bill->water_amount,
                'adjustment_total' => round((float) $bill->adjustment_total + $netAdjustmentFromLedger, 2),
                'total_amount' => round($totalAmount, 2),
                'due_date' => $bill->due_date,
                'created_at' => $bill->created_at,
                'is_meter_change' => (bool) $bill->is_meter_change,
                'old_meter_consumption' => $bill->old_meter_consumption ? (float) $bill->old_meter_consumption : null,
                'new_meter_consumption' => $bill->new_meter_consumption ? (float) $bill->new_meter_consumption : null,
                'rate_per_cum' => $ratePerCum,
                'rate_tier_range' => $rateTierRange,
            ],
            'readings' => [
                'previous' => (float) ($bill->previousReading?->reading_value ?? 0),
                'current' => (float) ($bill->currentReading?->reading_value ?? 0),
                'reading_date' => $bill->currentReading?->reading_date,
            ],
            'connection' => [
                'account_no' => $connection->account_no,
                'account_type' => $connection->accountType?->at_desc ?? 'Residential',
                'area' => $connection->area?->a_desc ?? 'N/A',
                'meter_serial' => $activeAssignment?->meter?->mtr_serial ?? 'N/A',
                'started_at' => $connection->started_at,
            ],
            'customer' => [
                'name' => $customerName,
                'address' => $fullAddress,
                'contact' => $customer?->contact_number ?? 'N/A',
            ],
            'period' => [
                'name' => $bill->period?->per_name ?? 'N/A',
                'start_date' => $bill->period?->start_date,
                'end_date' => $bill->period?->end_date,
            ],
            'old_meter' => $bill->is_meter_change ? [
                'serial' => $bill->oldMeterAssignment?->meter?->mtr_serial ?? 'N/A',
                'removal_read' => (float) ($bill->oldMeterAssignment?->removal_read ?? 0),
            ] : null,
            'arrears' => round($arrears, 2),
            'adjustments' => $adjustments->toArray(),
            'consumption_history' => $consumptionHistory->toArray(),
            'total_amount_due' => round($totalAmount + $arrears, 2),
        ];
    }

    /**
     * Get processing status summary for uploaded readings.
     *
     * @param  int|null  $periodId  Filter by period (through schedule relationship)
     * @param  int|null  $scheduleId  Filter by schedule
     * @return array Processing statistics
     */
    public function getUploadedReadingsProcessingStats(?int $periodId = null, ?int $scheduleId = null): array
    {
        $query = UploadedReading::query();

        // Filter by period (through schedule relationship)
        if ($periodId) {
            $query->whereHas('schedule', function ($q) use ($periodId) {
                $q->where('period_id', $periodId);
            });
        }

        // Filter by schedule if provided
        if ($scheduleId) {
            $query->where('schedule_id', $scheduleId);
        }

        $total = (clone $query)->count();
        $processed = (clone $query)->where('is_processed', true)->count();
        $unprocessed = (clone $query)->where('is_processed', false)->count();
        $canProcess = (clone $query)
            ->where('is_processed', false)
            ->whereNotNull('present_reading')
            ->whereNotNull('previous_reading')
            ->whereNotNull('connection_id')
            ->count();

        return [
            'total' => $total,
            'processed' => $processed,
            'unprocessed' => $unprocessed,
            'can_process' => $canProcess,
        ];
    }
}
