<?php

namespace App\Console\Commands;

use App\Models\Period;
use App\Models\SystemSetting;
use App\Services\Billing\PeriodService;
use App\Services\Billing\WaterRateService;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoCreatePeriod extends Command
{
    protected $signature = 'billing:auto-create-period';

    protected $description = 'Auto-create next month period and copy rates from current month';

    public function handle(
        PeriodService $periodService,
        WaterRateService $waterRateService,
        NotificationService $notificationService
    ): int {
        if (! SystemSetting::isEnabled(SystemSetting::AUTO_CREATE_PERIOD)) {
            $this->info('Auto-create period is disabled.');

            return self::SUCCESS;
        }

        $nextMonth = Carbon::now()->addMonth();
        $perCode = $nextMonth->format('Ym');

        // Check if next month already exists (idempotent)
        if (Period::where('per_code', $perCode)->exists()) {
            $this->info("Period {$perCode} already exists. Skipping.");

            return self::SUCCESS;
        }

        // Create the period
        $result = $periodService->createPeriod([
            'per_name' => $nextMonth->format('F Y'),
            'per_code' => $perCode,
            'start_date' => $nextMonth->startOfMonth()->format('Y-m-d'),
            'end_date' => $nextMonth->endOfMonth()->format('Y-m-d'),
            'grace_period' => 10,
        ]);

        if (! $result['success']) {
            $this->error('Failed to create period: '.$result['message']);
            Log::error('billing:auto-create-period failed', ['message' => $result['message']]);

            return self::FAILURE;
        }

        $newPeriodId = $result['data']['per_id'];

        // Copy rates from current month (or defaults) at 0% adjustment
        $currentPeriod = Period::where('is_closed', false)
            ->where('per_code', '!=', $perCode)
            ->orderBy('start_date', 'desc')
            ->first();

        $sourcePeriodId = $currentPeriod?->per_id;

        try {
            $ratesCopied = $waterRateService->copyRatesToPeriod($newPeriodId, 0, $sourcePeriodId);
        } catch (\DomainException $e) {
            $this->warn('Period created but no rates could be copied: '.$e->getMessage());
            $ratesCopied = 0;
        }

        // Auto-close the previous period
        $closedPeriodName = null;
        if ($currentPeriod) {
            $closeResult = $periodService->closePeriod($currentPeriod->per_id, null);
            if ($closeResult['success']) {
                $closedPeriodName = $currentPeriod->per_name;
                $this->info("Previous period {$currentPeriod->per_name} auto-closed.");
            } else {
                $this->warn("Could not close previous period: {$closeResult['message']}");
            }
        }

        // Notify admins
        $notificationService->notifyPeriodAutoCreated($nextMonth->format('F Y'), $closedPeriodName);

        $this->info("Period {$nextMonth->format('F Y')} created with {$ratesCopied} rates copied.");

        return self::SUCCESS;
    }
}
