<?php

use App\Models\Customer;
use App\Models\Status;
use App\Services\Customers\CustomerService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->customerService = new CustomerService;
});

test('getCustomerStats returns correct structure', function () {
    $stats = $this->customerService->getCustomerStats();

    expect($stats)->toBeArray()
        ->toHaveKeys(['total_customers', 'residential_count', 'total_current_bill', 'overdue_count']);
});

test('getCustomerStats calculates total customers correctly', function () {
    // Create 5 customers
    Customer::factory()->count(5)->create([
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $stats = $this->customerService->getCustomerStats();

    expect($stats['total_customers'])->toBe(5);
});

test('getCustomerStats calculates residential count correctly', function () {
    // Create 3 residential customers
    Customer::factory()->count(3)->create([
        'c_type' => 'RESIDENTIAL',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Create 2 commercial customers
    Customer::factory()->count(2)->create([
        'c_type' => 'COMMERCIAL',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $stats = $this->customerService->getCustomerStats();

    expect($stats['total_customers'])->toBe(5)
        ->and($stats['residential_count'])->toBe(3);
});

test('getCustomerStats returns zero when no data exists', function () {
    $stats = $this->customerService->getCustomerStats();

    expect($stats['total_customers'])->toBe(0)
        ->and($stats['residential_count'])->toBe(0)
        ->and($stats['total_current_bill'])->toBe('0.00')
        ->and($stats['overdue_count'])->toBe(0);
});

test('getCustomerStats formats total_current_bill correctly', function () {
    $stats = $this->customerService->getCustomerStats();

    // Should be a string formatted with 2 decimal places
    expect($stats['total_current_bill'])->toBeString()
        ->toMatch('/^\d+\.\d{2}$/');
});
