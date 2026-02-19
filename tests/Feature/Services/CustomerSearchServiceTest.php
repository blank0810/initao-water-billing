<?php

use App\Models\AccountType;
use App\Models\Area;
use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\MeterAssignment;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Services\Search\CustomerSearchService;

beforeEach(function () {
    $this->service = app(CustomerSearchService::class);
});

it('returns empty array for queries shorter than 2 characters', function () {
    $results = $this->service->search('a');
    expect($results)->toBeArray()->toBeEmpty();
});

it('finds customers by first name', function () {
    $status = Status::factory()->create(['stat_desc' => 'ACTIVE']);
    $address = ConsumerAddress::factory()->create(['stat_id' => $status->stat_id]);

    $customer = Customer::factory()->create([
        'cust_first_name' => 'Juan',
        'cust_last_name' => 'Dela Cruz',
        'ca_id' => $address->ca_id,
        'stat_id' => $status->stat_id,
    ]);

    $results = $this->service->search('Juan');
    expect($results)->toHaveCount(1);
    expect($results[0]['customer_id'])->toBe($customer->cust_id);
    expect($results[0]['name'])->toContain('Juan');
});

it('finds customers by resolution number', function () {
    $status = Status::factory()->create(['stat_desc' => 'ACTIVE']);
    $address = ConsumerAddress::factory()->create(['stat_id' => $status->stat_id]);

    $customer = Customer::factory()->create([
        'cust_first_name' => 'Maria',
        'cust_last_name' => 'Santos',
        'resolution_no' => 'INITAO-MS-1234567890',
        'ca_id' => $address->ca_id,
        'stat_id' => $status->stat_id,
    ]);

    $results = $this->service->search('INITAO-MS');
    expect($results)->toHaveCount(1);
    expect($results[0]['resolution_no'])->toBe('INITAO-MS-1234567890');
});

it('finds customers by meter serial number', function () {
    $status = Status::factory()->create(['stat_desc' => 'ACTIVE']);
    $address = ConsumerAddress::factory()->create(['stat_id' => $status->stat_id]);
    $accountType = AccountType::factory()->create(['stat_id' => $status->stat_id]);
    $area = Area::factory()->create(['stat_id' => $status->stat_id]);

    $customer = Customer::factory()->create([
        'cust_first_name' => 'Pedro',
        'cust_last_name' => 'Reyes',
        'ca_id' => $address->ca_id,
        'stat_id' => $status->stat_id,
    ]);

    $connection = ServiceConnection::factory()->create([
        'customer_id' => $customer->cust_id,
        'address_id' => $address->ca_id,
        'account_type_id' => $accountType->at_id,
        'area_id' => $area->a_id,
        'stat_id' => $status->stat_id,
    ]);

    $meter = Meter::factory()->create([
        'mtr_serial' => 'MTR-2024-999',
        'stat_id' => $status->stat_id,
    ]);

    MeterAssignment::factory()->create([
        'connection_id' => $connection->connection_id,
        'meter_id' => $meter->mtr_id,
        'installed_at' => now(),
    ]);

    $results = $this->service->search('MTR-2024-999');
    expect($results)->toHaveCount(1);
    expect($results[0]['meter_serial'])->toBe('MTR-2024-999');
});

it('limits results to 10', function () {
    $status = Status::factory()->create(['stat_desc' => 'ACTIVE']);
    $address = ConsumerAddress::factory()->create(['stat_id' => $status->stat_id]);

    Customer::factory()->count(15)->create([
        'cust_first_name' => 'TestName',
        'cust_last_name' => 'User',
        'ca_id' => $address->ca_id,
        'stat_id' => $status->stat_id,
    ]);

    $results = $this->service->search('TestName');
    expect($results)->toHaveCount(10);
});

it('returns correct response shape', function () {
    $status = Status::factory()->create(['stat_desc' => 'ACTIVE']);
    $address = ConsumerAddress::factory()->create(['stat_id' => $status->stat_id]);
    $accountType = AccountType::factory()->create(['stat_id' => $status->stat_id]);
    $area = Area::factory()->create(['stat_id' => $status->stat_id]);

    $customer = Customer::factory()->create([
        'cust_first_name' => 'Ana',
        'cust_last_name' => 'Garcia',
        'resolution_no' => 'INITAO-AG-111',
        'ca_id' => $address->ca_id,
        'stat_id' => $status->stat_id,
    ]);

    $connection = ServiceConnection::factory()->create([
        'customer_id' => $customer->cust_id,
        'address_id' => $address->ca_id,
        'account_no' => 'ACC-00001',
        'account_type_id' => $accountType->at_id,
        'area_id' => $area->a_id,
        'stat_id' => $status->stat_id,
    ]);

    $meter = Meter::factory()->create([
        'mtr_serial' => 'MTR-001',
        'stat_id' => $status->stat_id,
    ]);

    MeterAssignment::factory()->create([
        'connection_id' => $connection->connection_id,
        'meter_id' => $meter->mtr_id,
        'installed_at' => now(),
    ]);

    $results = $this->service->search('Ana');
    expect($results[0])->toHaveKeys([
        'customer_id', 'name', 'resolution_no', 'account_no',
        'meter_serial', 'barangay', 'status',
    ]);
});
