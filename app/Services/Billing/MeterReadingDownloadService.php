<?php

namespace App\Services\Billing;

use App\Models\CustomerLedger;
use App\Models\Period;
use App\Models\ReadingSchedule;
use App\Models\ReadingScheduleEntry;
use App\Models\Status;
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

        $consumers = $entries->map(function ($entry) use ($previousPeriod, $scheduleAreaMap, $periodId) {
            $connection = $entry->serviceConnection;
            $customer = $connection?->customer;
            $address = $connection?->address;
            $connectionId = $connection?->connection_id;

            // Get current active meter assignment
            $activeMeterAssignment = $connection?->meterAssignments->first();
            $meter = $activeMeterAssignment?->meter;

            // Get previous reading value from meterReadings relationship
            // Fall back to install_read from MeterAssignment if no previous reading exists
            $previousReadingValue = null;
            if ($activeMeterAssignment) {
                if ($previousPeriod) {
                    $previousReading = $activeMeterAssignment->meterReadings
                        ->where('period_id', $previousPeriod->per_id)
                        ->first();
                    $previousReadingValue = $previousReading?->reading_value;
                }

                // If no previous reading found, use install_read as fallback
                if ($previousReadingValue === null) {
                    $previousReadingValue = $activeMeterAssignment->install_read;
                }
            }

            // Calculate arrear: sum of unpaid bills from previous periods
            // Arrear = total bill amount (debit) - total payments (credit) for previous periods
            $arrear = 0;
            if ($connectionId && $periodId) {
                // Get total debits (bills) from CustomerLedger for previous periods
                $totalDebits = CustomerLedger::where('connection_id', $connectionId)
                    ->where('period_id', '<', $periodId)
                    ->where('source_type', 'BILL')
                    ->sum('debit');

                // Get total credits (payments) from CustomerLedger for previous periods
                $totalCredits = CustomerLedger::where('connection_id', $connectionId)
                    ->where('period_id', '<', $periodId)
                    ->where('source_type', 'PAYMENT')
                    ->sum('credit');

                $arrear = max(0, (float) $totalDebits - (float) $totalCredits);
            }

            // Calculate penalty: sum of penalties from CustomerLedger for previous periods
            $penalty = 0;
            if ($connectionId && $periodId) {
                // Penalties are typically charges with description containing 'PENALTY'
                // or source_type 'CHARGE' with penalty-related descriptions
                $penalty = (float) CustomerLedger::where('connection_id', $connectionId)
                    ->where('period_id', '<', $periodId)
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
                'entry_status' => $entry->status,
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
            ],
            'rates' => $ratesData->toArray(),
        ];
    }
}
