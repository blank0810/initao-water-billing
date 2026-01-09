<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use App\Models\Period;
use App\Models\Status;
use App\Models\WaterRate;
use App\Services\Billing\WaterRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RateController extends Controller
{
    protected WaterRateService $rateService;

    public function __construct(WaterRateService $rateService)
    {
        $this->rateService = $rateService;
    }

    /**
     * Get all periods with their rate information.
     */
    public function getPeriods(): JsonResponse
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $periods = Period::select('per_id', 'per_name', 'start_date', 'is_closed', 'stat_id')
            ->withCount(['waterRates' => function ($query) use ($activeStatusId) {
                $query->where('stat_id', $activeStatusId);
            }])
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($period) use ($activeStatusId) {
                return [
                    'per_id' => $period->per_id,
                    'per_name' => $period->per_name,
                    'start_date' => $period->start_date,
                    'is_closed' => (bool) $period->is_closed,
                    'is_active' => $period->stat_id === $activeStatusId && ! $period->is_closed,
                    'has_custom_rates' => $period->water_rates_count > 0,
                    'rate_count' => $period->water_rates_count,
                ];
            });

        // Find the current active period (not closed, active status)
        $activePeriod = $periods->first(fn ($p) => $p['is_active']);
        $activePeriodId = $activePeriod ? $activePeriod['per_id'] : null;

        return response()->json([
            'periods' => $periods,
            'activePeriodId' => $activePeriodId,
        ]);
    }

    /**
     * Get account types (classes) for dropdown.
     */
    public function getAccountTypes(): JsonResponse
    {
        $accountTypes = AccountType::where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->orderBy('at_id')
            ->get(['at_id', 'at_desc']);

        // Find Residential account type as default
        $residentialType = $accountTypes->first(fn ($at) => strtolower($at->at_desc) === 'residential');
        $defaultAccountTypeId = $residentialType ? $residentialType->at_id : ($accountTypes->first()?->at_id ?? null);

        return response()->json([
            'accountTypes' => $accountTypes,
            'defaultAccountTypeId' => $defaultAccountTypeId,
        ]);
    }

    /**
     * Get rates for a specific period or default rates.
     */
    public function getRatesForPeriod(string $periodId): JsonResponse
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Get account types for class names
        $accountTypes = AccountType::where('stat_id', $activeStatusId)
            ->orderBy('at_id')
            ->get(['at_id', 'at_desc']);

        $accountTypesMap = $accountTypes->pluck('at_desc', 'at_id');

        // Find Residential account type as default
        $residentialType = $accountTypes->first(fn ($at) => strtolower($at->at_desc) === 'residential');
        $defaultAccountTypeId = $residentialType ? $residentialType->at_id : ($accountTypes->first()?->at_id ?? null);

        if ($periodId === 'default') {
            $rates = WaterRate::whereNull('period_id')
                ->where('stat_id', $activeStatusId)
                ->orderBy('class_id')
                ->orderBy('range_id')
                ->get();

            // Add class name to each rate
            $rates = $rates->map(function ($rate) use ($accountTypesMap) {
                $rate->class_name = $accountTypesMap[$rate->class_id] ?? 'Unknown';

                return $rate;
            });

            return response()->json([
                'rates' => $rates,
                'hasCustomRates' => false,
                'periodId' => null,
                'accountTypes' => $accountTypesMap,
                'defaultAccountTypeId' => $defaultAccountTypeId,
            ]);
        }

        // Check if period has custom rates
        $customRates = WaterRate::where('period_id', $periodId)
            ->where('stat_id', $activeStatusId)
            ->orderBy('class_id')
            ->orderBy('range_id')
            ->get();

        if ($customRates->isNotEmpty()) {
            $customRates = $customRates->map(function ($rate) use ($accountTypesMap) {
                $rate->class_name = $accountTypesMap[$rate->class_id] ?? 'Unknown';

                return $rate;
            });

            return response()->json([
                'rates' => $customRates,
                'hasCustomRates' => true,
                'periodId' => (int) $periodId,
                'accountTypes' => $accountTypesMap,
                'defaultAccountTypeId' => $defaultAccountTypeId,
            ]);
        }

        // Fall back to default rates
        $defaultRates = WaterRate::whereNull('period_id')
            ->where('stat_id', $activeStatusId)
            ->orderBy('class_id')
            ->orderBy('range_id')
            ->get();

        $defaultRates = $defaultRates->map(function ($rate) use ($accountTypesMap) {
            $rate->class_name = $accountTypesMap[$rate->class_id] ?? 'Unknown';

            return $rate;
        });

        return response()->json([
            'rates' => $defaultRates,
            'hasCustomRates' => false,
            'periodId' => (int) $periodId,
            'accountTypes' => $accountTypesMap,
            'defaultAccountTypeId' => $defaultAccountTypeId,
        ]);
    }

    /**
     * Copy default rates to a specific period.
     */
    public function copyRatesToPeriod(Request $request, int $periodId): JsonResponse
    {
        $request->validate([
            'apply_increase' => 'boolean',
            'increase_percent' => 'numeric|min:-100|max:1000',
        ]);

        $period = Period::findOrFail($periodId);

        if ($period->is_closed) {
            return response()->json([
                'message' => 'Cannot modify rates for a closed period.',
            ], 422);
        }

        // Check if period already has custom rates
        $existingCount = WaterRate::where('period_id', $periodId)->count();
        if ($existingCount > 0) {
            return response()->json([
                'message' => 'This period already has custom rates.',
            ], 422);
        }

        $applyIncrease = $request->boolean('apply_increase', false);
        $increasePercent = $applyIncrease ? $request->input('increase_percent', 0) : 0;

        try {
            $count = $this->rateService->copyRatesToPeriod($periodId, $increasePercent);

            return response()->json([
                'message' => 'Rates copied successfully.',
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to copy rates: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a single rate.
     */
    public function updateRate(Request $request, int $rateId): JsonResponse
    {
        $request->validate([
            'rate_val' => 'required|numeric|min:0',
            'rate_inc' => 'required|numeric|min:0',
            'range_min' => 'required|integer|min:0',
            'range_max' => 'required|integer|min:0',
            'period_id' => 'nullable|integer',
        ]);

        $rate = WaterRate::findOrFail($rateId);

        // Check if editing a default rate for a specific period
        if ($rate->period_id === null && $request->filled('period_id')) {
            $periodId = $request->input('period_id');
            $period = Period::findOrFail($periodId);

            if ($period->is_closed) {
                return response()->json([
                    'message' => 'Cannot modify rates for a closed period.',
                ], 422);
            }

            // Check if a period-specific rate already exists
            $existingRate = WaterRate::where('period_id', $periodId)
                ->where('class_id', $rate->class_id)
                ->where('range_id', $rate->range_id)
                ->first();

            if ($existingRate) {
                $existingRate->update([
                    'rate_val' => $request->input('rate_val'),
                    'rate_inc' => $request->input('rate_inc'),
                    'range_min' => $request->input('range_min'),
                    'range_max' => $request->input('range_max'),
                ]);
            } else {
                WaterRate::create([
                    'period_id' => $periodId,
                    'class_id' => $rate->class_id,
                    'range_id' => $rate->range_id,
                    'range_min' => $request->input('range_min'),
                    'range_max' => $request->input('range_max'),
                    'rate_val' => $request->input('rate_val'),
                    'rate_inc' => $request->input('rate_inc'),
                    'stat_id' => $rate->stat_id,
                ]);
            }
        } else {
            // Update the existing rate
            if ($rate->period_id !== null) {
                $period = Period::find($rate->period_id);
                if ($period && $period->is_closed) {
                    return response()->json([
                        'message' => 'Cannot modify rates for a closed period.',
                    ], 422);
                }
            }

            $rate->update([
                'rate_val' => $request->input('rate_val'),
                'rate_inc' => $request->input('rate_inc'),
                'range_min' => $request->input('range_min'),
                'range_max' => $request->input('range_max'),
            ]);
        }

        return response()->json([
            'message' => 'Rate updated successfully.',
        ]);
    }

    /**
     * Upload rates from CSV.
     */
    public function uploadRates(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
            'period_id' => 'nullable|integer',
        ]);

        $periodId = $request->filled('period_id') ? $request->input('period_id') : null;

        if ($periodId) {
            $period = Period::findOrFail($periodId);
            if ($period->is_closed) {
                return response()->json([
                    'message' => 'Cannot upload rates for a closed period.',
                ], 422);
            }
        }

        $file = $request->file('file');
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        DB::beginTransaction();
        try {
            $handle = fopen($file->getPathname(), 'r');
            $header = fgetcsv($handle);

            // Normalize header names
            $header = array_map(fn ($col) => strtolower(trim($col)), $header);

            // Required columns
            $requiredCols = ['class_id', 'range_id', 'range_min', 'range_max', 'rate_val', 'rate_inc'];
            $colIndexes = [];

            foreach ($requiredCols as $col) {
                $index = array_search($col, $header);
                if ($index === false) {
                    fclose($handle);

                    return response()->json([
                        'message' => "CSV must contain '{$col}' column.",
                    ], 422);
                }
                $colIndexes[$col] = $index;
            }

            $count = 0;
            while (($row = fgetcsv($handle)) !== false) {
                $classId = (int) ($row[$colIndexes['class_id']] ?? 0);
                $rangeId = (int) ($row[$colIndexes['range_id']] ?? 0);

                if ($classId <= 0 || $rangeId <= 0) {
                    continue;
                }

                WaterRate::updateOrCreate(
                    [
                        'period_id' => $periodId,
                        'class_id' => $classId,
                        'range_id' => $rangeId,
                    ],
                    [
                        'range_min' => (int) ($row[$colIndexes['range_min']] ?? 0),
                        'range_max' => (int) ($row[$colIndexes['range_max']] ?? 999),
                        'rate_val' => (float) ($row[$colIndexes['rate_val']] ?? 0),
                        'rate_inc' => (float) ($row[$colIndexes['rate_inc']] ?? 0),
                        'stat_id' => $activeStatusId,
                    ]
                );

                $count++;
            }

            fclose($handle);
            DB::commit();

            return response()->json([
                'message' => 'Rates uploaded successfully.',
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to upload rates: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download CSV template for rate upload.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="water_rates_template.csv"',
        ];

        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $defaultRates = WaterRate::whereNull('period_id')
            ->where('stat_id', $activeStatusId)
            ->orderBy('class_id')
            ->orderBy('range_id')
            ->get();

        $accountTypes = AccountType::where('stat_id', $activeStatusId)
            ->pluck('at_desc', 'at_id');

        return response()->stream(function () use ($defaultRates, $accountTypes) {
            $handle = fopen('php://output', 'w');

            // Write header
            fputcsv($handle, ['class_id', 'class_name', 'range_id', 'range_min', 'range_max', 'rate_val', 'rate_inc']);

            // Write data
            foreach ($defaultRates as $rate) {
                fputcsv($handle, [
                    $rate->class_id,
                    $accountTypes[$rate->class_id] ?? '',
                    $rate->range_id,
                    $rate->range_min,
                    $rate->range_max,
                    $rate->rate_val,
                    $rate->rate_inc,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
