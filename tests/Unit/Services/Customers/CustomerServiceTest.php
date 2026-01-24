<?php

namespace Tests\Unit\Services\Customers;

use App\Models\Barangay;
use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\Province;
use App\Models\Purok;
use App\Models\Status;
use App\Models\Town;
use App\Services\Customers\CustomerService;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->service = new CustomerService;
});

test('getCustomerList returns all required fields', function () {
    // Get or create Town and Province
    $province = Province::firstOrCreate(
        ['prov_desc' => 'Test Province'],
        ['stat_id' => Status::getIdByDescription(Status::ACTIVE)]
    );

    $town = Town::firstOrCreate(
        ['t_desc' => 'Test Town'],
        ['prov_id' => $province->prov_id, 'stat_id' => Status::getIdByDescription(Status::ACTIVE)]
    );

    // Create barangay
    $barangay = Barangay::factory()->create();

    // Create purok
    $purok = Purok::create([
        'p_desc' => 'Test Purok',
        'b_id' => $barangay->b_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Create consumer address
    $address = ConsumerAddress::create([
        'p_id' => $purok->p_id,
        'b_id' => $barangay->b_id,
        't_id' => $town->t_id,
        'prov_id' => $province->prov_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Create a customer
    $customer = Customer::create([
        'cust_first_name' => 'JUAN',
        'cust_middle_name' => 'DELA',
        'cust_last_name' => 'CRUZ',
        'contact_number' => '09171234567',
        'land_mark' => 'NEAR CHURCH',
        'c_type' => 'RESIDENTIAL',
        'resolution_no' => 'INITAO-JDC-1234567890',
        'ca_id' => $address->ca_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'create_date' => now(),
    ]);

    $request = new Request;
    $result = $this->service->getCustomerList($request);

    expect($result)->toHaveKey('data')
        ->and($result['data']->count())->toBeGreaterThanOrEqual(1);

    $customerData = $result['data']->firstWhere('cust_id', $customer->cust_id);

    // Verify all required fields are present
    expect($customerData)->toHaveKey('cust_id')
        ->and($customerData)->toHaveKey('cust_first_name')
        ->and($customerData)->toHaveKey('cust_middle_name')
        ->and($customerData)->toHaveKey('cust_last_name')
        ->and($customerData)->toHaveKey('contact_number')
        ->and($customerData)->toHaveKey('land_mark');

    // Verify field values
    expect($customerData['cust_first_name'])->toBe('JUAN')
        ->and($customerData['cust_middle_name'])->toBe('DELA')
        ->and($customerData['cust_last_name'])->toBe('CRUZ')
        ->and($customerData['contact_number'])->toBe('09171234567')
        ->and($customerData['land_mark'])->toBe('NEAR CHURCH');
});

test('getCustomerList handles empty contact_number and middle_name', function () {
    // Get or create Town and Province
    $province = Province::firstOrCreate(
        ['prov_desc' => 'Test Province'],
        ['stat_id' => Status::getIdByDescription(Status::ACTIVE)]
    );

    $town = Town::firstOrCreate(
        ['t_desc' => 'Test Town'],
        ['prov_id' => $province->prov_id, 'stat_id' => Status::getIdByDescription(Status::ACTIVE)]
    );

    // Create barangay
    $barangay = Barangay::factory()->create();

    // Create purok
    $purok = Purok::create([
        'p_desc' => 'Test Purok 2',
        'b_id' => $barangay->b_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Create consumer address
    $address = ConsumerAddress::create([
        'p_id' => $purok->p_id,
        'b_id' => $barangay->b_id,
        't_id' => $town->t_id,
        'prov_id' => $province->prov_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Create a customer without contact_number and middle_name
    $customer = Customer::create([
        'cust_first_name' => 'PEDRO',
        'cust_middle_name' => null,
        'cust_last_name' => 'SANTOS',
        'contact_number' => null,
        'land_mark' => 'NEAR PLAZA',
        'c_type' => 'COMMERCIAL',
        'resolution_no' => 'INITAO-PS-9876543210',
        'ca_id' => $address->ca_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'create_date' => now(),
    ]);

    $request = new Request;
    $result = $this->service->getCustomerList($request);

    $customerData = $result['data']->firstWhere('cust_id', $customer->cust_id);

    // Verify empty fields return empty strings
    expect($customerData['cust_middle_name'])->toBe('')
        ->and($customerData['contact_number'])->toBe('');
});

test('getCustomerList includes meter_no and current_bill fields', function () {
    // Get or create Town and Province
    $province = Province::firstOrCreate(
        ['prov_desc' => 'Test Province'],
        ['stat_id' => Status::getIdByDescription(Status::ACTIVE)]
    );

    $town = Town::firstOrCreate(
        ['t_desc' => 'Test Town'],
        ['prov_id' => $province->prov_id, 'stat_id' => Status::getIdByDescription(Status::ACTIVE)]
    );

    // Create barangay
    $barangay = Barangay::factory()->create();

    // Create purok
    $purok = Purok::create([
        'p_desc' => 'Test Purok 3',
        'b_id' => $barangay->b_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Create consumer address
    $address = ConsumerAddress::create([
        'p_id' => $purok->p_id,
        'b_id' => $barangay->b_id,
        't_id' => $town->t_id,
        'prov_id' => $province->prov_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Create a customer
    $customer = Customer::create([
        'cust_first_name' => 'MARIA',
        'cust_middle_name' => 'DELA',
        'cust_last_name' => 'CRUZ',
        'contact_number' => '09181234567',
        'land_mark' => 'NEAR MARKET',
        'c_type' => 'RESIDENTIAL',
        'resolution_no' => 'INITAO-MDC-1111111111',
        'ca_id' => $address->ca_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'create_date' => now(),
    ]);

    $request = new Request;
    $result = $this->service->getCustomerList($request);

    $customerData = $result['data']->firstWhere('cust_id', $customer->cust_id);

    // Verify meter_no and current_bill fields are present
    expect($customerData)->toHaveKey('meter_no')
        ->and($customerData)->toHaveKey('current_bill');

    // Verify default values when no meter/bill exists
    expect($customerData['meter_no'])->toBe('N/A')
        ->and($customerData['current_bill'])->toBe('₱0.00');
});
