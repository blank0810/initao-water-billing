<?php

namespace Tests\Unit\Services\Admin\Config;

use App\Models\AccountType;
use App\Models\Status;
use App\Models\WaterRate;
use App\Services\Admin\Config\WaterRateService;

beforeEach(function () {
    // Create required statuses for foreign keys
    Status::create(['stat_id' => 1, 'stat_desc' => 'PENDING']);
    Status::create(['stat_id' => 2, 'stat_desc' => 'ACTIVE']);
    Status::create(['stat_id' => 3, 'stat_desc' => 'INACTIVE']);

    $this->service = new WaterRateService;
    $this->accountType = AccountType::factory()->create(['at_desc' => 'Residential']);
});

test('getAllRates returns rates grouped by account type', function () {
    WaterRate::factory()->count(4)->create([
        'class_id' => $this->accountType->at_id,
        'period_id' => null,
    ]);

    $result = $this->service->getAllRates();

    expect($result)->toHaveKey('data')
        ->and($result['data'])->toHaveKey('Residential')
        ->and($result['data']['Residential'])->toHaveCount(4);
});

test('createOrUpdateRateTier creates new tier', function () {
    $data = [
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 0,
        'range_max' => 10,
        'rate_val' => 100.00,
        'rate_inc' => 0.00,
    ];

    $tier = $this->service->createOrUpdateRateTier($data);

    expect($tier->range_min)->toBe(0)
        ->and($tier->range_max)->toBe(10)
        ->and((float) $tier->rate_val)->toBe(100.00);
});

test('createOrUpdateRateTier updates existing tier', function () {
    $existing = WaterRate::create([
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 0,
        'range_max' => 10,
        'rate_val' => 100.00,
        'rate_inc' => 0.00,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $updated = $this->service->createOrUpdateRateTier([
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 0,
        'range_max' => 10,
        'rate_val' => 120.00, // Updated
        'rate_inc' => 0.00,
    ]);

    expect($updated->wr_id)->toBe($existing->wr_id)
        ->and((float) $updated->rate_val)->toBe(120.00);
});

test('validateNoRangeOverlap throws exception for overlapping ranges', function () {
    // Create tier: 0-10
    WaterRate::create([
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 0,
        'range_max' => 10,
        'rate_val' => 100.00,
        'rate_inc' => 0.00,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Try to create overlapping tier: 5-15
    $newTier = [
        'range_min' => 5,
        'range_max' => 15,
    ];

    expect(fn () => $this->service->validateNoRangeOverlap(
        $this->accountType->at_id,
        null,
        $newTier
    ))->toThrow(\DomainException::class, 'overlaps');
});

test('validateNoRangeOverlap passes for non-overlapping ranges', function () {
    // Create tier: 0-10
    WaterRate::create([
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 0,
        'range_max' => 10,
        'rate_val' => 100.00,
        'rate_inc' => 0.00,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Create non-overlapping tier: 11-20
    $newTier = [
        'range_min' => 11,
        'range_max' => 20,
    ];

    $result = $this->service->validateNoRangeOverlap(
        $this->accountType->at_id,
        null,
        $newTier
    );

    expect($result)->toBeTrue();
});
