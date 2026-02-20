<?php

use App\Models\Period;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Database\Seeders\StatusSeeder;

beforeEach(function () {
    $this->seed(StatusSeeder::class);
});

test('auto-create period is skipped when toggle is off', function () {
    // Toggle is off (not created, defaults to false)
    $this->artisan('billing:auto-create-period')
        ->expectsOutput('Auto-create period is disabled.')
        ->assertExitCode(0);

    // Also test with explicit false
    SystemSetting::create([
        'key' => SystemSetting::AUTO_CREATE_PERIOD,
        'value' => 'false',
        'type' => 'boolean',
        'group' => 'automation',
    ]);

    $this->artisan('billing:auto-create-period')
        ->expectsOutput('Auto-create period is disabled.')
        ->assertExitCode(0);

    // No period should have been created
    $nextMonth = Carbon::now()->addMonth();
    $perCode = $nextMonth->format('Ym');
    expect(Period::where('per_code', $perCode)->exists())->toBeFalse();
});

test('auto-create period creates next month when toggle is on', function () {
    SystemSetting::create([
        'key' => SystemSetting::AUTO_CREATE_PERIOD,
        'value' => 'true',
        'type' => 'boolean',
        'group' => 'automation',
    ]);

    $nextMonth = Carbon::now()->addMonth();
    $perCode = $nextMonth->format('Ym');
    $perName = $nextMonth->format('F Y');

    $this->artisan('billing:auto-create-period')
        ->assertExitCode(0);

    // Assert period was created with correct attributes
    $period = Period::where('per_code', $perCode)->first();
    expect($period)->not->toBeNull();
    expect($period->per_name)->toBe($perName);
    expect($period->is_closed)->toBeFalse();
    expect($period->grace_period)->toBe(10);
});

test('auto-create period is idempotent', function () {
    SystemSetting::create([
        'key' => SystemSetting::AUTO_CREATE_PERIOD,
        'value' => 'true',
        'type' => 'boolean',
        'group' => 'automation',
    ]);

    $nextMonth = Carbon::now()->addMonth();
    $perCode = $nextMonth->format('Ym');

    // Manually create the next month's period first
    $activeStatusId = \App\Models\Status::getIdByDescription(\App\Models\Status::ACTIVE);
    Period::create([
        'per_name' => $nextMonth->format('F Y'),
        'per_code' => $perCode,
        'start_date' => $nextMonth->copy()->startOfMonth()->format('Y-m-d'),
        'end_date' => $nextMonth->copy()->endOfMonth()->format('Y-m-d'),
        'grace_period' => 10,
        'is_closed' => false,
        'stat_id' => $activeStatusId,
    ]);

    // Run the command - it should skip
    $this->artisan('billing:auto-create-period')
        ->expectsOutput("Period {$perCode} already exists. Skipping.")
        ->assertExitCode(0);

    // Assert only one period with that per_code exists
    expect(Period::where('per_code', $perCode)->count())->toBe(1);
});
