<?php

use App\Services\Notification\NotificationService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    app(NotificationService::class)->cleanupOldNotifications(90);
})->daily()->description('Clean up old notifications');

Schedule::command('billing:auto-create-period')
    ->lastDayOfMonth('00:00')
    ->description('Auto-create next month billing period and copy rates');

Schedule::command('billing:auto-apply-penalties')
    ->daily()
    ->description('Auto-apply penalties to overdue bills');
