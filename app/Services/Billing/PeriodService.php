<?php

namespace App\Services\Billing;

use App\Models\Period;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PeriodService
{
    /**
     * Get all periods with statistics for table display.
     */
    public function getAllPeriodsWithStats(): Collection
    {
        return Period::withCount(['waterBillHistory', 'meterReadings', 'waterRates'])
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($period) {
                return [
                    'per_id' => $period->per_id,
                    'per_name' => $period->per_name,
                    'per_code' => $period->per_code,
                    'start_date' => $period->start_date->format('Y-m-d'),
                    'end_date' => $period->end_date->format('Y-m-d'),
                    'date_range' => $period->start_date->format('M d').' - '.$period->end_date->format('M d, Y'),
                    'grace_period' => $period->grace_period,
                    'is_closed' => $period->is_closed,
                    'closed_at' => $period->closed_at?->format('Y-m-d H:i:s'),
                    'bills_count' => $period->water_bill_history_count,
                    'readings_count' => $period->meter_readings_count,
                    'rates_count' => $period->water_rates_count,
                    'has_custom_rates' => $period->water_rates_count > 0,
                    'can_delete' => $this->canDeletePeriod($period),
                    'can_edit' => ! $period->is_closed,
                    'can_reopen' => $this->canReopenPeriod($period),
                ];
            });
    }

    /**
     * Get period details by ID.
     */
    public function getPeriodDetails(int $periodId): ?array
    {
        $period = Period::withCount(['waterBillHistory', 'meterReadings', 'waterRates'])
            ->with('closedByUser:id,name')
            ->find($periodId);

        if (! $period) {
            return null;
        }

        return [
            'per_id' => $period->per_id,
            'per_name' => $period->per_name,
            'per_code' => $period->per_code,
            'start_date' => $period->start_date->format('Y-m-d'),
            'end_date' => $period->end_date->format('Y-m-d'),
            'grace_period' => $period->grace_period,
            'is_closed' => $period->is_closed,
            'closed_at' => $period->closed_at?->format('Y-m-d H:i:s'),
            'closed_by_name' => $period->closedByUser?->name,
            'bills_count' => $period->water_bill_history_count,
            'readings_count' => $period->meter_readings_count,
            'rates_count' => $period->water_rates_count,
            'has_custom_rates' => $period->water_rates_count > 0,
            'can_delete' => $this->canDeletePeriod($period),
            'can_edit' => ! $period->is_closed,
            'can_reopen' => $this->canReopenPeriod($period),
        ];
    }

    /**
     * Create a new period.
     */
    public function createPeriod(array $data): array
    {
        // Validate date range doesn't overlap
        $overlap = Period::where(function ($query) use ($data) {
            $query->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
                ->orWhere(function ($q) use ($data) {
                    $q->where('start_date', '<=', $data['start_date'])
                        ->where('end_date', '>=', $data['end_date']);
                });
        })->exists();

        if ($overlap) {
            return [
                'success' => false,
                'message' => 'Date range overlaps with an existing period.',
            ];
        }

        // Check if per_code already exists
        if (Period::where('per_code', $data['per_code'])->exists()) {
            return [
                'success' => false,
                'message' => 'Period code already exists.',
            ];
        }

        return DB::transaction(function () use ($data) {
            $period = Period::create([
                'per_name' => $data['per_name'],
                'per_code' => $data['per_code'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'grace_period' => $data['grace_period'] ?? 10,
                'is_closed' => false,
                'stat_id' => Status::getIdByDescription(Status::ACTIVE),
            ]);

            return [
                'success' => true,
                'message' => 'Period created successfully. Use "Create Period Rates" to set up rates for this period.',
                'data' => $this->getPeriodDetails($period->per_id),
            ];
        });
    }

    /**
     * Update a period.
     */
    public function updatePeriod(int $periodId, array $data): array
    {
        $period = Period::find($periodId);

        if (! $period) {
            return [
                'success' => false,
                'message' => 'Period not found.',
            ];
        }

        if ($period->is_closed) {
            return [
                'success' => false,
                'message' => 'Cannot edit a closed period.',
            ];
        }

        // Validate date range doesn't overlap (excluding current period)
        if (isset($data['start_date']) || isset($data['end_date'])) {
            $startDate = $data['start_date'] ?? $period->start_date;
            $endDate = $data['end_date'] ?? $period->end_date;

            $overlap = Period::where('per_id', '!=', $periodId)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })->exists();

            if ($overlap) {
                return [
                    'success' => false,
                    'message' => 'Date range overlaps with an existing period.',
                ];
            }
        }

        // Check per_code uniqueness (excluding current)
        if (isset($data['per_code']) && $data['per_code'] !== $period->per_code) {
            if (Period::where('per_code', $data['per_code'])->where('per_id', '!=', $periodId)->exists()) {
                return [
                    'success' => false,
                    'message' => 'Period code already exists.',
                ];
            }
        }

        $period->update([
            'per_name' => $data['per_name'] ?? $period->per_name,
            'per_code' => $data['per_code'] ?? $period->per_code,
            'start_date' => $data['start_date'] ?? $period->start_date,
            'end_date' => $data['end_date'] ?? $period->end_date,
            'grace_period' => $data['grace_period'] ?? $period->grace_period,
        ]);

        return [
            'success' => true,
            'message' => 'Period updated successfully.',
            'data' => $this->getPeriodDetails($period->per_id),
        ];
    }

    /**
     * Delete a period.
     */
    public function deletePeriod(int $periodId): array
    {
        $period = Period::withCount(['waterBillHistory', 'meterReadings'])
            ->find($periodId);

        if (! $period) {
            return [
                'success' => false,
                'message' => 'Period not found.',
            ];
        }

        if ($period->is_closed) {
            return [
                'success' => false,
                'message' => 'Cannot delete a closed period.',
            ];
        }

        if ($period->water_bill_history_count > 0) {
            return [
                'success' => false,
                'message' => 'Cannot delete period with existing bills.',
            ];
        }

        if ($period->meter_readings_count > 0) {
            return [
                'success' => false,
                'message' => 'Cannot delete period with existing meter readings.',
            ];
        }

        // Delete associated water rates first
        $period->waterRates()->delete();

        $period->delete();

        return [
            'success' => true,
            'message' => 'Period deleted successfully.',
        ];
    }

    /**
     * Close a period.
     */
    public function closePeriod(int $periodId, int $userId): array
    {
        $period = Period::find($periodId);

        if (! $period) {
            return [
                'success' => false,
                'message' => 'Period not found.',
            ];
        }

        if ($period->is_closed) {
            return [
                'success' => false,
                'message' => 'Period is already closed.',
            ];
        }

        $period->update([
            'is_closed' => true,
            'closed_at' => now(),
            'closed_by' => $userId,
        ]);

        return [
            'success' => true,
            'message' => 'Period closed successfully.',
            'data' => $this->getPeriodDetails($period->per_id),
        ];
    }

    /**
     * Reopen a period (only within 24 hours of closing).
     */
    public function openPeriod(int $periodId): array
    {
        $period = Period::find($periodId);

        if (! $period) {
            return [
                'success' => false,
                'message' => 'Period not found.',
            ];
        }

        if (! $period->is_closed) {
            return [
                'success' => false,
                'message' => 'Period is already open.',
            ];
        }

        if (! $this->canReopenPeriod($period)) {
            return [
                'success' => false,
                'message' => 'Period can only be reopened within 24 hours of closing.',
            ];
        }

        $period->update([
            'is_closed' => false,
            'closed_at' => null,
            'closed_by' => null,
        ]);

        return [
            'success' => true,
            'message' => 'Period reopened successfully.',
            'data' => $this->getPeriodDetails($period->per_id),
        ];
    }

    /**
     * Generate period code from date.
     */
    public function generatePeriodCode(string $date): string
    {
        return Carbon::parse($date)->format('Ym');
    }

    /**
     * Generate period name from date.
     */
    public function generatePeriodName(string $date): string
    {
        return Carbon::parse($date)->format('F Y');
    }

    /**
     * Get statistics summary.
     */
    public function getStatsSummary(): array
    {
        $total = Period::count();
        $open = Period::where('is_closed', false)->count();
        $closed = Period::where('is_closed', true)->count();
        $withRates = Period::whereHas('waterRates')->count();

        return [
            'total' => $total,
            'open' => $open,
            'closed' => $closed,
            'with_rates' => $withRates,
        ];
    }

    /**
     * Check if a period can be deleted.
     */
    private function canDeletePeriod(Period $period): bool
    {
        if ($period->is_closed) {
            return false;
        }

        // Use counts if already loaded, otherwise check
        $billsCount = $period->water_bill_history_count ?? $period->waterBillHistory()->count();
        $readingsCount = $period->meter_readings_count ?? $period->meterReadings()->count();

        return $billsCount === 0 && $readingsCount === 0;
    }

    /**
     * Check if a period can be reopened.
     */
    private function canReopenPeriod(Period $period): bool
    {
        if (! $period->is_closed || ! $period->closed_at) {
            return false;
        }

        // Allow reopening within 24 hours
        return $period->closed_at->diffInHours(now()) <= 24;
    }
}
