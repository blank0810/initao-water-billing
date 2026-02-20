<?php

namespace App\Console\Commands;

use App\Models\ReadingSchedule;
use App\Services\Notification\NotificationService;
use Illuminate\Console\Command;

class NotifyOverdueSchedules extends Command
{
    protected $signature = 'billing:notify-overdue-schedules';

    protected $description = 'Send notifications for reading schedules past their end date';

    public function handle(NotificationService $notificationService): int
    {
        $overdueSchedules = ReadingSchedule::with(['area', 'period'])
            ->where('status', 'in_progress')
            ->whereDate('scheduled_end_date', '<', now()->toDateString())
            ->get();

        $notified = 0;
        foreach ($overdueSchedules as $schedule) {
            $notificationService->notifyScheduleOverdue(
                $schedule->area?->a_desc ?? 'Unknown',
                $schedule->period?->per_name ?? 'Unknown',
                $schedule->schedule_id
            );
            $notified++;
        }

        $this->info("Sent {$notified} overdue schedule notification(s).");

        return self::SUCCESS;
    }
}
