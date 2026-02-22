<?php

namespace Tests\Feature\Services\Customer;

use App\Models\Barangay;
use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\Province;
use App\Models\Purok;
use App\Models\Status;
use App\Models\Town;
use App\Services\Customers\CustomerApprovalService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Status::create(['stat_id' => 1, 'stat_desc' => 'ACTIVE']);
    Status::create(['stat_id' => 2, 'stat_desc' => 'INACTIVE']);
    Status::create(['stat_id' => 3, 'stat_desc' => 'PENDING']);
    Status::create(['stat_id' => 4, 'stat_desc' => 'VERIFIED']);
    Status::create(['stat_id' => 5, 'stat_desc' => 'PAID']);
    Status::create(['stat_id' => 6, 'stat_desc' => 'SCHEDULED']);
    Status::create(['stat_id' => 7, 'stat_desc' => 'CONNECTED']);
    Status::create(['stat_id' => 8, 'stat_desc' => 'REJECTED']);
    Status::create(['stat_id' => 9, 'stat_desc' => 'CANCELLED']);
    Status::create(['stat_id' => 10, 'stat_desc' => 'SUSPENDED']);
    Status::create(['stat_id' => 11, 'stat_desc' => 'DISCONNECTED']);
    Status::create(['stat_id' => 12, 'stat_desc' => 'OVERDUE']);

    // Create address dependencies required by Customer
    $province = Province::create([
        'prov_desc' => 'Test Province',
        'prov_code' => 'TPROV',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $town = Town::create([
        't_desc' => 'Test Town',
        't_code' => 'TTOWN',
        'prov_id' => $province->prov_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $barangay = Barangay::create([
        'b_desc' => 'Test Barangay',
        'b_code' => 'TBRGY',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $purok = Purok::create([
        'p_desc' => 'Test Purok',
        'b_id' => $barangay->b_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $this->testAddress = ConsumerAddress::create([
        'p_id' => $purok->p_id,
        'b_id' => $barangay->b_id,
        't_id' => $town->t_id,
        'prov_id' => $province->prov_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $this->service = app(CustomerApprovalService::class);
});

test('reactivateCustomer transitions INACTIVE customer to ACTIVE', function () {
    $customer = Customer::create([
        'cust_first_name' => 'EVE',
        'cust_last_name' => 'TAYLOR',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-001',
    ]);

    $result = $this->service->reactivateCustomer($customer->cust_id);

    expect($result->stat_id)->toBe(Status::getIdByDescription(Status::ACTIVE))
        ->and($result->status->stat_desc)->toBe('ACTIVE');
});

test('reactivateCustomer transitions SUSPENDED customer to ACTIVE', function () {
    $customer = Customer::create([
        'cust_first_name' => 'SUSPENDED',
        'cust_last_name' => 'USER',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::SUSPENDED),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-002',
    ]);

    $result = $this->service->reactivateCustomer($customer->cust_id);

    expect($result->stat_id)->toBe(Status::getIdByDescription(Status::ACTIVE))
        ->and($result->status->stat_desc)->toBe('ACTIVE');
});

test('reactivateCustomer transitions PENDING customer to ACTIVE', function () {
    $customer = Customer::create([
        'cust_first_name' => 'PENDING',
        'cust_last_name' => 'USER',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-003',
    ]);

    $result = $this->service->reactivateCustomer($customer->cust_id);

    expect($result->stat_id)->toBe(Status::getIdByDescription(Status::ACTIVE))
        ->and($result->status->stat_desc)->toBe('ACTIVE');
});

test('reactivateCustomer throws exception if customer is already ACTIVE', function () {
    $customer = Customer::create([
        'cust_first_name' => 'ALREADY',
        'cust_last_name' => 'ACTIVE',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-004',
    ]);

    $this->service->reactivateCustomer($customer->cust_id);
})->throws(\Exception::class, 'Only INACTIVE, SUSPENDED, or PENDING customers can be reactivated.');
