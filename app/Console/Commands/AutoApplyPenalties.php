<?php

namespace App\Console\Commands;

use App\Models\SystemSetting;
use App\Services\Billing\PenaltyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoApplyPenalties extends Command
{
    protected $signature = 'billing:auto-apply-penalties';

    protected $description = 'Automatically apply penalties to overdue bills';

    public function handle(PenaltyService $penaltyService): int
    {
        if (! SystemSetting::isEnabled(SystemSetting::AUTO_APPLY_PENALTIES)) {
            $this->info('Auto-apply penalties is disabled.');

            return self::SUCCESS;
        }

        // PenaltyService::processAllOverdueBills already:
        // - Finds overdue bills (due_date < today, stat_id = ACTIVE, period open)
        // - Skips bills that already have penalties (hasExistingPenalty check)
        // - Creates penalty charges + ledger entries
        // - Sends notification via NotificationService
        // This is inherently one-time-per-bill since hasExistingPenalty() prevents duplicates
        $result = $penaltyService->processAllOverdueBills(0); // userId=0 for system

        if (! empty($result['errors'])) {
            Log::error('billing:auto-apply-penalties encountered errors', ['errors' => $result['errors']]);
            $this->error('Some penalties failed to apply. Check logs for details.');

            if ($result['processed'] === 0) {
                return self::FAILURE;
            }
        }

        $this->info($result['message']);
        $this->info("Processed: {$result['processed']}, Skipped: {$result['skipped']}");

        return self::SUCCESS;
    }
}
