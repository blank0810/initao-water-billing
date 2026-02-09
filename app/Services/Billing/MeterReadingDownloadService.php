<?php

namespace App\Services\Billing;

use App\Models\CustomerLedger;
use App\Models\MeterAssignment;
use App\Models\MeterReading;
use App\Models\Period;
use App\Models\ReadingSchedule;
use App\Models\ReadingScheduleEntry;
use App\Models\Status;
use App\Models\WaterBillHistory;
use App\Models\WaterRate;

class MeterReadingDownloadService
{
    /**
     * Get consumer information for active reading schedules assigned to a user.
     *
     * @param  int  $userId  The user ID (meter reader)
     * @return array Consumer information with schedule details
     */
    public function getConsumerInfoByUser(int $userId): array
    {
        // Get active reading schedules for this user (pending or in_progress)
        $activeSchedules = ReadingSchedule::where('reader_id', $userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->with('period', 'area')
            ->get();

        if ($activeSchedules->isEmpty()) {
            return [
                'schedules' => [],
                'consumers' => [],
            ];
        }

        $scheduleIds = $activeSchedules->pluck('schedule_id')->toArray();

        // Build a map of schedule_id to area description
        $scheduleAreaMap = $activeSchedules->pluck('area.a_desc', 'schedule_id')->toArray();

        // Get all entries for active schedules with all needed relationships
        $entries = ReadingScheduleEntry::with([
            'serviceConnection.customer',
            'serviceConnection.address.barangay',
            'serviceConnection.address.purok',
            'serviceConnection.address.town',
            'serviceConnection.accountType',
            'serviceConnection.status',
            'serviceConnection.meterAssignments' => function ($query) {
                $query->whereNull('removed_at')
                    ->with(['meter', 'meterReadings']);
            },
            'schedule.period',
            'status',
        ])
            ->whereIn('schedule_id', $scheduleIds)
            ->orderBy('schedule_id')
            ->orderBy('sequence_order')
            ->get();

        // Get the period from the first schedule for previous reading lookup
        $schedule = $activeSchedules->first();
        $periodId = $schedule?->period_id;

        // Find previous period for previous reading
        $previousPeriod = null;
        if ($periodId) {
            $currentPeriod = Period::find($periodId);
            if ($currentPeriod) {
                $previousPeriod = Period::where('start_date', '<', $currentPeriod->start_date)
                    ->orderBy('start_date', 'desc')
                    ->first();
            }
        }

        // Pre-fetch removed meter assignments for connections with change_meter flag
        // This avoids N+1 queries inside the map() callback
        $meterChangeConnectionIds = $entries
            ->filter(fn ($e) => $e->serviceConnection?->change_meter)
            ->pluck('serviceConnection.connection_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $removedAssignmentsMap = [];
        if (! empty($meterChangeConnectionIds)) {
            $removedAssignments = MeterAssignment::whereIn('connection_id', $meterChangeConnectionIds)
                ->whereNotNull('removed_at')
                ->whereNotNull('removal_read')
                ->orderBy('removed_at', 'desc')
                ->get()
                ->groupBy('connection_id');

            // Take only the most recent removed assignment per connection
            foreach ($removedAssignments as $connId => $assignments) {
                $removedAssignmentsMap[$connId] = $assignments->first();
            }
        }

        $consumers = $entries->map(function ($entry) use ($previousPeriod, $scheduleAreaMap, $periodId, $removedAssignmentsMap) {
            $connection = $entry->serviceConnection;
            $customer = $connection?->customer;
            $address = $connection?->address;
            $connectionId = $connection?->connection_id;

            // Get current active meter assignment
            $activeMeterAssignment = $connection?->meterAssignments->first();
            $meter = $activeMeterAssignment?->meter;

            // ============================================
            // METER CHANGE DATA (change_meter = true)
            // ============================================
            $isMeterChange = $connection?->change_meter ?? false;
            $removalRead = null;
            $installRead = null;

            if ($isMeterChange && $connectionId) {
                // Use pre-fetched removed assignment (avoids N+1 query)
                $previousAssignment = $removedAssignmentsMap[$connectionId] ?? null;

                if ($previousAssignment) {
                    $removalRead = (float) $previousAssignment->removal_read;
                }

                // Get install_read from current assignment
                if ($activeMeterAssignment) {
                    $installRead = (float) $activeMeterAssignment->install_read;
                }
            }
            // ============================================

            // Get previous reading value from the previous period's bill current reading
            // Fall back to install_read from MeterAssignment if no previous reading exists
            $previousReadingValue = null;
            if ($connectionId) {
                if ($previousPeriod) {
                    // Get the current reading from the previous period's water bill
                    // This is the most accurate source since it was the actual reading used for billing
                    $previousBill = WaterBillHistory::where('connection_id', $connectionId)
                        ->where('period_id', $previousPeriod->per_id)
                        ->with('currentReading')
                        ->first();

                    if ($previousBill && $previousBill->currentReading) {
                        $previousReadingValue = $previousBill->currentReading->reading_value;
                    }
                }

                // If no previous bill found, try to get the latest reading
                if ($previousReadingValue === null) {
                    if ($isMeterChange && isset($removedAssignmentsMap[$connectionId])) {
                        // For meter change: get last reading from OLD (removed) meter
                        $oldAssignment = $removedAssignmentsMap[$connectionId];
                        $latestReading = MeterReading::where('assignment_id', $oldAssignment->assignment_id)
                            ->whereNotNull('period_id')
                            ->orderBy('reading_date', 'desc')
                            ->orderBy('reading_id', 'desc')
                            ->first();
                        $previousReadingValue = $latestReading?->reading_value;
                    } elseif ($activeMeterAssignment) {
                        // Normal case: get latest reading from current meter
                        $latestReading = $activeMeterAssignment->meterReadings()
                            ->whereNotNull('period_id')
                            ->orderBy('reading_date', 'desc')
                            ->orderBy('reading_id', 'desc')
                            ->first();
                        $previousReadingValue = $latestReading?->reading_value;
                    }
                }

                // If still no reading found, use install_read as fallback (only for non-meter-change)
                if ($previousReadingValue === null && $activeMeterAssignment && ! $isMeterChange) {
                    $previousReadingValue = $activeMeterAssignment->install_read;
                }
            }

            // Calculate arrear: unpaid water bills from previous periods (excludes penalties)
            // Arrear = BILL debits - PAYMENT credits
            $arrear = 0;
            $penalty = 0;
            if ($connectionId && $periodId) {
                // Get total BILL debits from CustomerLedger for previous periods
                $totalBillDebits = CustomerLedger::where('connection_id', $connectionId)
                    ->where('period_id', '<', $periodId)
                    ->where('stat_id', '=', 2)
                    ->where('source_type', 'BILL')
                    ->sum('debit');

                // Get total PAYMENT credits from CustomerLedger for previous periods
                $totalPaymentCredits = CustomerLedger::where('connection_id', $connectionId)
                    ->where('period_id', '<', $periodId)
                    ->where('stat_id', '=', 2)
                    ->where('source_type', 'PAYMENT')
                    ->sum('credit');

                $arrear = max(0, (float) $totalBillDebits - (float) $totalPaymentCredits);

                // Calculate penalty separately (CHARGE entries with penalty descriptions)
                // Penalty is NOT included in arrear - tracked separately
                $penalty = (float) CustomerLedger::where('connection_id', $connectionId)
                    ->where('period_id', '<', $periodId)
                    ->where('source_type', 'CHARGE')
                    ->where(function ($query) {
                        $query->where('description', 'LIKE', '%PENALTY%')
                            ->orWhere('description', 'LIKE', '%Penalty%')
                            ->orWhere('description', 'LIKE', '%penalty%');
                    })
                    ->sum('debit');
            }

            // Build full address string
            $addressParts = [];
            if ($address?->purok) {
                $addressParts[] = $address->purok->p_desc ?? '';
            }
            if ($address?->barangay) {
                $addressParts[] = $address->barangay->b_desc ?? '';
            }
            if ($address?->town) {
                $addressParts[] = $address->town->t_desc ?? '';
            }
            $fullAddress = implode(', ', array_filter($addressParts));

            // Build customer full name
            $customerName = '';
            if ($customer) {
                $customerName = trim(sprintf(
                    '%s, %s %s',
                    $customer->cust_last_name ?? '',
                    $customer->cust_first_name ?? '',
                    $customer->cust_middle_name ?? ''
                ));
            }

            return [
                'schedule_id' => $entry->schedule_id,
                'connection_id' => $connectionId,
                'consumer_account_no' => $connection?->account_no,
                'customer_name' => $customerName,
                'address' => $fullAddress,
                'area_desc' => $scheduleAreaMap[$entry->schedule_id] ?? null,
                'account_type_desc' => $connection?->accountType?->at_desc,
                'connection_status' => $connection?->status?->stat_desc,
                'meter_serial' => $meter?->mtr_serial,
                'previous_reading' => $previousReadingValue !== null ? (float) $previousReadingValue : null,
                'arrear' => $arrear,
                'penalty' => $penalty,
                'sequence_order' => $entry->sequence_order,
                'entry_status' => $entry->status?->stat_desc,
                // Meter change fields for mobile app
                'is_meter_change' => $isMeterChange,
                'removal_read' => $removalRead,
                'install_read' => $installRead,
            ];
        });

        // Build schedule summary
        $scheduleSummary = $activeSchedules->map(function ($schedule) {
            return [
                'schedule_id' => $schedule->schedule_id,
                'period_name' => $schedule->period?->per_name,
                'area_name' => $schedule->area?->a_name,
                'area_desc' => $schedule->area?->a_desc,
                'status' => $schedule->status,
                'scheduled_start_date' => $schedule->scheduled_start_date?->format('Y-m-d'),
                'scheduled_end_date' => $schedule->scheduled_end_date?->format('Y-m-d'),
            ];
        });

        return [
            'schedules' => $scheduleSummary->toArray(),
            'consumers' => $consumers->values()->toArray(),
        ];
    }

    /**
     * Get water rates for the current active period.
     *
     * @return array Water rates information with period details
     */
    public function getCurrentPeriodWaterRates(): array
    {
        // Get current active period (open period with latest start date)
        $currentPeriod = Period::where('is_closed', false)
            ->orderBy('start_date', 'desc')
            ->first();

        if (! $currentPeriod) {
            return [
                'period' => null,
                'rates' => [],
            ];
        }

        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Get rates for the current period, fallback to default if none exist
        $rates = WaterRate::with('accountType')
            ->where('period_id', $currentPeriod->per_id)
            ->where('stat_id', $activeStatusId)
            ->orderBy('class_id')
            ->orderBy('range_id')
            ->get();

        // Fallback to default rates if no period-specific rates
        if ($rates->isEmpty()) {
            $rates = WaterRate::with('accountType')
                ->whereNull('period_id')
                ->where('stat_id', $activeStatusId)
                ->orderBy('class_id')
                ->orderBy('range_id')
                ->get();
        }

        $ratesData = $rates->map(function ($rate) {
            return [
                'account_type_desc' => $rate->accountType?->at_desc,
                'range_min' => $rate->range_min,
                'range_max' => $rate->range_max,
                'rate_val' => (float) $rate->rate_val,
                'rate_increment' => (float) $rate->rate_inc,
            ];
        });

        return [
            'period' => [
                'per_id' => $currentPeriod->per_id,
                'per_name' => $currentPeriod->per_name,
                'per_code' => $currentPeriod->per_code,
                'start_date' => $currentPeriod->start_date->format('Y-m-d'),
                'end_date' => $currentPeriod->end_date->format('Y-m-d'),
                'grace_period' => $currentPeriod->grace_period,
            ],
            'rates' => $ratesData->toArray(),
        ];
    }

    /**
     * Get water rates for a specific period.
     *
     * @param  int  $periodId  The period ID
     * @return array Water rates information with period details
     */
    public function getWaterRatesByPeriod(int $periodId): array
    {
        $period = Period::find($periodId);

        if (! $period) {
            return [
                'period' => null,
                'rates' => [],
            ];
        }

        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Get rates for the specified period, fallback to default if none exist
        $rates = WaterRate::with('accountType')
            ->where('period_id', $periodId)
            ->where('stat_id', $activeStatusId)
            ->orderBy('class_id')
            ->orderBy('range_id')
            ->get();

        // Fallback to default rates if no period-specific rates
        if ($rates->isEmpty()) {
            $rates = WaterRate::with('accountType')
                ->whereNull('period_id')
                ->where('stat_id', $activeStatusId)
                ->orderBy('class_id')
                ->orderBy('range_id')
                ->get();
        }

        $ratesData = $rates->map(function ($rate) {
            return [
                'account_type_desc' => $rate->accountType?->at_desc,
                'range_min' => $rate->range_min,
                'range_max' => $rate->range_max,
                'rate_val' => (float) $rate->rate_val,
                'rate_increment' => (float) $rate->rate_inc,
            ];
        });

        return [
            'period' => [
                'per_id' => $period->per_id,
                'per_name' => $period->per_name,
                'per_code' => $period->per_code,
                'start_date' => $period->start_date->format('Y-m-d'),
                'end_date' => $period->end_date->format('Y-m-d'),
                'grace_period' => $period->grace_period,
            ],
            'rates' => $ratesData->toArray(),
        ];
    }
}
