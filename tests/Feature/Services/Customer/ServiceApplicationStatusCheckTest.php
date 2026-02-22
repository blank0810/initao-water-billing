<?php

namespace Tests\Feature\Services\Customer;

use App\Models\Barangay;
use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\Purok;
use App\Models\Status;
use App\Services\ServiceApplication\ServiceApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    Status::create(['stat_id' => 1, 'stat_desc' => 'ACTIVE']);
    Status::create(['stat_id' => 2, 'stat_desc' => 'INACTIVE']);
    Status::create(['stat_id' => 3, 'stat_desc' => 'PENDING']);
    Status::create(['stat_id' => 4, 'stat_desc' => 'VERIFIED']);
    Status::create(['stat_id' => 5, 'stat_desc' => 'PAID']);

    // Create address dependencies required by Customer.
    // Province and Town must have IDs of 1 because ServiceApplicationService
    // hardcodes t_id => 1 and prov_id => 1 when creating service addresses.
    // We use DB::table()->insert() to force specific primary key values,
    // since the Eloquent models don't include primary keys in $fillable.
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

    $this->barangayId = $barangay->b_id;
    $this->purokId = $purok->p_id;

    $this->service = app(ServiceApplicationService::class);
});

test('createApplication throws for PENDING existing customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'JANE',
        'cust_last_name' => 'DOE',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-001',
    ]);

    $this->service->createApplication(
        'existing',
        ['customerId' => $customer->cust_id],
        ['barangay' => $this->barangayId, 'purok' => $this->purokId, 'landmark' => 'Test'],
        1
    );
})->throws(\Exception::class, 'Customer must be ACTIVE to create a service application.');

test('createApplication throws for INACTIVE existing customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'BOB',
        'cust_last_name' => 'SMITH',
        'ca_id' => $this->testAddress->ca_id,
        'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-002',
    ]);

    $this->service->createApplication(
        'existing',
        ['customerId' => $customer->cust_id],
        ['barangay' => $this->barangayId, 'purok' => $this->purokId, 'landmark' => 'Test'],
        1
    );
})->throws(\Exception::class, 'Customer must be ACTIVE to create a service application.');
