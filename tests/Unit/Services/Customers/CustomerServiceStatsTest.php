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

test('getCustomerStats calculates bill amounts from unpaid ledger entries', function () {
    // Create customers
    $customer1 = Customer::factory()->create([
        'c_type' => 'RESIDENTIAL',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);
    $customer2 = Customer::factory()->create([
        'c_type' => 'COMMERCIAL',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Create bills in CustomerLedger - one paid, two unpaid
    \DB::table('CustomerLedger')->insert([
        [
            'customer_id' => $customer1->cust_id,
            'source_type' => 'BILL',
            'source_id' => 1,
            'txn_date' => now(),
            'debit' => 500.00,
            'credit' => 0,
            'stat_id' => Status::getIdByDescription(Status::PAID), // Paid
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'customer_id' => $customer1->cust_id,
            'source_type' => 'BILL',
            'source_id' => 3,
            'txn_date' => now(),
            'debit' => 300.00,
            'credit' => 0,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE), // Unpaid
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'customer_id' => $customer2->cust_id,
            'source_type' => 'BILL',
            'source_id' => 2,
            'txn_date' => now(),
            'debit' => 750.00,
            'credit' => 0,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE), // Unpaid
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    $stats = $this->customerService->getCustomerStats();

    // Only ACTIVE (unpaid) BILL debits are counted: 300 + 750 = 1050.00
    expect((float) $stats['total_current_bill'])->toBe(1050.00);
});

test('getCustomerStats with different residential vs commercial customers', function () {
    // Create 7 residential customers
    Customer::factory()->count(7)->create([
        'c_type' => 'RESIDENTIAL',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Create 3 commercial customers
    Customer::factory()->count(3)->create([
        'c_type' => 'COMMERCIAL',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Create 1 industrial customer
    Customer::factory()->count(1)->create([
        'c_type' => 'INDUSTRIAL',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $stats = $this->customerService->getCustomerStats();

    expect($stats['total_customers'])->toBe(11)
        ->and($stats['residential_count'])->toBe(7);
});

test('getCustomerStats counts overdue bills correctly', function () {
    // Create customers
    $customer1 = Customer::factory()->create(['stat_id' => Status::getIdByDescription(Status::ACTIVE)]);
    $customer2 = Customer::factory()->create(['stat_id' => Status::getIdByDescription(Status::ACTIVE)]);
    $customer3 = Customer::factory()->create(['stat_id' => Status::getIdByDescription(Status::ACTIVE)]);

    // Create overdue bills in water_bill_history
    $overdueBill1 = \DB::table('water_bill_history')->insertGetId([
        'connection_id' => 1,
        'period_id' => 1,
        'prev_reading_id' => 1,
        'curr_reading_id' => 2,
        'consumption' => 10.000,
        'water_amount' => 500.00,
        'due_date' => now()->subDays(10), // Overdue
        'adjustment_total' => 0.00,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $overdueBill2 = \DB::table('water_bill_history')->insertGetId([
        'connection_id' => 2,
        'period_id' => 2,
        'prev_reading_id' => 3,
        'curr_reading_id' => 4,
        'consumption' => 15.000,
        'water_amount' => 750.00,
        'due_date' => now()->subDays(5), // Overdue
        'adjustment_total' => 0.00,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create current bill (not overdue)
    $currentBill = \DB::table('water_bill_history')->insertGetId([
        'connection_id' => 3,
        'period_id' => 3,
        'prev_reading_id' => 5,
        'curr_reading_id' => 6,
        'consumption' => 6.000,
        'water_amount' => 300.00,
        'due_date' => now()->addDays(5), // Not overdue
        'adjustment_total' => 0.00,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Add unpaid ledger entries for overdue bills
    \DB::table('CustomerLedger')->insert([
        [
            'customer_id' => $customer1->cust_id,
            'source_type' => 'BILL',
            'source_id' => $overdueBill1,
            'txn_date' => now(),
            'debit' => 500.00,
            'credit' => 0,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'customer_id' => $customer2->cust_id,
            'source_type' => 'BILL',
            'source_id' => $overdueBill2,
            'txn_date' => now(),
            'debit' => 750.00,
            'credit' => 0,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    // Add current bill ledger entry
    \DB::table('CustomerLedger')->insert([
        'customer_id' => $customer3->cust_id,
        'source_type' => 'BILL',
        'source_id' => $currentBill,
        'txn_date' => now(),
        'debit' => 300.00,
        'credit' => 0,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $stats = $this->customerService->getCustomerStats();

    // Should count 2 customers with overdue bills
    expect($stats['overdue_count'])->toBe(2);
});
