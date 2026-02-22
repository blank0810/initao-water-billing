<?php

namespace Tests\Feature\Services\Customer;

use App\Models\Barangay;
use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\Purok;
use App\Models\Status;
use App\Services\Customers\CustomerApprovalService;
use App\Services\Customers\CustomerStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    Status::create(['stat_id' => 1, 'stat_desc' => 'ACTIVE']);
    Status::create(['stat_id' => 2, 'stat_desc' => 'INACTIVE']);
    Status::create(['stat_id' => 3, 'stat_desc' => 'PENDING']);
    Status::create(['stat_id' => 10, 'stat_desc' => 'SUSPENDED']);

    // Create address dependencies required by Customer model (ca_id foreign key)
    DB::table('province')->insert([
        'prov_id' => 1,
        'prov_desc' => 'Test Province',
        'stat_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('town')->insert([
        't_id' => 1,
        't_desc' => 'Test Town',
        'stat_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
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
        't_id' => 1,
        'prov_id' => 1,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $this->statusService = app(CustomerStatusService::class);
    $this->approvalService = app(CustomerApprovalService::class);
});

test('full customer lifecycle: ACTIVE -> INACTIVE -> reactivated ACTIVE', function () {
    // 1. Customer starts as ACTIVE (created via service application)
    $customer = Customer::create([
        'cust_first_name' => 'LIFECYCLE',
        'cust_last_name' => 'TEST',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-LIFE-001',
    ]);

    $actions = $this->statusService->getCustomerAllowedActions($customer);
    expect($actions)->toContain('create_application')->toContain('edit');

    // 2. Manually set INACTIVE (simulating deactivation)
    $customer->update(['stat_id' => Status::getIdByDescription(Status::INACTIVE)]);
    $customer = $customer->fresh('status');

    $actions = $this->statusService->getCustomerAllowedActions($customer);
    expect($actions)->toContain('reactivate')->toContain('process_payment');
    expect($actions)->not->toContain('create_application');

    // 3. Reactivate -> ACTIVE again
    $customer = $this->approvalService->reactivateCustomer($customer->cust_id);
    expect($this->statusService->getCustomerStatusDescription($customer))->toBe('ACTIVE');

    $actions = $this->statusService->getCustomerAllowedActions($customer);
    expect($actions)->toContain('create_application');
});

test('SUSPENDED customer can be reactivated', function () {
    $customer = Customer::create([
        'cust_first_name' => 'SUSPENDED',
        'cust_last_name' => 'LIFECYCLE',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::SUSPENDED),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-SUSP-001',
    ]);

    $actions = $this->statusService->getCustomerAllowedActions($customer);
    expect($actions)->toContain('view')
        ->toContain('process_payment')
        ->toContain('reactivate')
        ->not->toContain('edit')
        ->not->toContain('create_application');

    // Reactivate SUSPENDED -> ACTIVE
    $customer = $this->approvalService->reactivateCustomer($customer->cust_id);
    expect($this->statusService->getCustomerStatusDescription($customer))->toBe('ACTIVE');
});

test('PENDING customer is blocked from all mutations', function () {
    $customer = Customer::create([
        'cust_first_name' => 'BLOCKED',
        'cust_last_name' => 'PENDING',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-BLOCK-001',
    ]);

    expect(fn () => $this->statusService->assertCustomerCanCreateApplication($customer))
        ->toThrow(\Exception::class);

    expect(fn () => $this->statusService->assertCustomerCanProcessPayment($customer))
        ->toThrow(\Exception::class);

    expect(fn () => $this->statusService->assertCustomerCanEdit($customer))
        ->toThrow(\Exception::class);

    expect(fn () => $this->statusService->assertCustomerCanDelete($customer))
        ->toThrow(\Exception::class);

    // But PENDING can be reactivated
    $actions = $this->statusService->getCustomerAllowedActions($customer);
    expect($actions)->toContain('reactivate');
});
