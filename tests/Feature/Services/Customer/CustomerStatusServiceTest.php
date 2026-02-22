<?php

namespace Tests\Feature\Services\Customer;

use App\Models\Barangay;
use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\Province;
use App\Models\Purok;
use App\Models\Status;
use App\Models\Town;
use App\Services\Customers\CustomerStatusService;
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

    $this->service = app(CustomerStatusService::class);
});

test('assertCustomerCanCreateApplication passes for ACTIVE customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'JOHN',
        'cust_last_name' => 'DOE',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-001',
    ]);

    $this->service->assertCustomerCanCreateApplication($customer);
    expect(true)->toBeTrue();
});

test('assertCustomerCanCreateApplication throws for PENDING customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'JANE',
        'cust_last_name' => 'DOE',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-002',
    ]);

    $this->service->assertCustomerCanCreateApplication($customer);
})->throws(\Exception::class, 'Customer must be ACTIVE to create a service application. Current status: PENDING.');

test('assertCustomerCanCreateApplication throws for INACTIVE customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'BOB',
        'cust_last_name' => 'SMITH',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-003',
    ]);

    $this->service->assertCustomerCanCreateApplication($customer);
})->throws(\Exception::class, 'Customer must be ACTIVE to create a service application. Current status: INACTIVE.');

test('assertCustomerCanProcessPayment passes for ACTIVE customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'JOHN',
        'cust_last_name' => 'DOE',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-004',
    ]);

    $this->service->assertCustomerCanProcessPayment($customer);
    expect(true)->toBeTrue();
});

test('assertCustomerCanProcessPayment passes for INACTIVE customer paying existing bills', function () {
    $customer = Customer::create([
        'cust_first_name' => 'OLD',
        'cust_last_name' => 'CUSTOMER',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-005',
    ]);

    $this->service->assertCustomerCanProcessPayment($customer);
    expect(true)->toBeTrue();
});

test('assertCustomerCanProcessPayment throws for PENDING customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'NEW',
        'cust_last_name' => 'PERSON',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-006',
    ]);

    $this->service->assertCustomerCanProcessPayment($customer);
})->throws(\Exception::class, 'Customer must be ACTIVE or INACTIVE to process payments. Current status: PENDING.');

test('assertCustomerCanEdit passes for ACTIVE customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'EDIT',
        'cust_last_name' => 'ME',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-007',
    ]);

    $this->service->assertCustomerCanEdit($customer);
    expect(true)->toBeTrue();
});

test('assertCustomerCanEdit throws for PENDING customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'PENDING',
        'cust_last_name' => 'EDIT',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-008',
    ]);

    $this->service->assertCustomerCanEdit($customer);
})->throws(\Exception::class, 'Customer must be ACTIVE to edit. Current status: PENDING.');

test('assertCustomerCanEdit throws for INACTIVE customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'INACTIVE',
        'cust_last_name' => 'EDIT',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-009',
    ]);

    $this->service->assertCustomerCanEdit($customer);
})->throws(\Exception::class, 'Customer must be ACTIVE to edit. Current status: INACTIVE.');

test('getCustomerStatusDescription returns correct description', function () {
    $customer = Customer::create([
        'cust_first_name' => 'STATUS',
        'cust_last_name' => 'CHECK',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-010',
    ]);

    expect($this->service->getCustomerStatusDescription($customer))->toBe('ACTIVE');
});

test('getCustomerAllowedActions returns full actions for ACTIVE customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'FULL',
        'cust_last_name' => 'ACCESS',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-011',
    ]);

    $actions = $this->service->getCustomerAllowedActions($customer);

    expect($actions)->toContain('create_application')
        ->toContain('process_payment')
        ->toContain('edit')
        ->toContain('delete');
});

test('getCustomerAllowedActions returns view and reactivate for PENDING customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'VIEW',
        'cust_last_name' => 'ONLY',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-012',
    ]);

    $actions = $this->service->getCustomerAllowedActions($customer);

    expect($actions)->toContain('view')
        ->toContain('reactivate')
        ->not->toContain('create_application')
        ->not->toContain('process_payment')
        ->not->toContain('edit')
        ->not->toContain('delete');
});

test('getCustomerAllowedActions returns limited actions for INACTIVE customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'LIMITED',
        'cust_last_name' => 'ACCESS',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-013',
    ]);

    $actions = $this->service->getCustomerAllowedActions($customer);

    expect($actions)->toContain('view')
        ->toContain('process_payment')
        ->toContain('reactivate')
        ->not->toContain('create_application')
        ->not->toContain('edit')
        ->not->toContain('delete');
});

test('getCustomerAllowedActions returns limited actions for SUSPENDED customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'SUSPENDED',
        'cust_last_name' => 'ACCESS',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::SUSPENDED),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-014',
    ]);

    $actions = $this->service->getCustomerAllowedActions($customer);

    expect($actions)->toContain('view')
        ->toContain('process_payment')
        ->toContain('reactivate')
        ->not->toContain('create_application')
        ->not->toContain('edit')
        ->not->toContain('delete');
});
