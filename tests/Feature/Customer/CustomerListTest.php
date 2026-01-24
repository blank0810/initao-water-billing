<?php

namespace Tests\Feature\Customer;

use App\Models\Customer;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create required statuses for foreign keys
    Status::create(['stat_id' => 1, 'stat_desc' => 'ACTIVE']);
    Status::create(['stat_id' => 2, 'stat_desc' => 'INACTIVE']);
    Status::create(['stat_id' => 3, 'stat_desc' => 'PENDING']);

    // Create required user type
    \DB::table('user_types')->insert([
        'ut_id' => 3,
        'ut_desc' => 'ADMIN',
        'stat_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create user with customers.view permission
    $this->user = User::factory()->create();

    // Create and assign permission
    $permissionId = \DB::table('permissions')->insertGetId([
        'permission_name' => 'customers.view',
        'description' => 'View customers',
    ]);

    $roleId = \DB::table('roles')->insertGetId([
        'role_name' => 'Test Customer Viewer',
    ]);

    \DB::table('role_permissions')->insert([
        'role_id' => $roleId,
        'permission_id' => $permissionId,
    ]);

    \DB::table('user_roles')->insert([
        'user_id' => $this->user->id,
        'role_id' => $roleId,
    ]);

    $this->actingAs($this->user);
});

test('customer list page loads successfully', function () {
    $response = $this->get(route('customer.list'));

    $response->assertOk();
    $response->assertViewIs('pages.customer.customer-list');
});

test('customer list api returns json with correct structure', function () {
    // Create 5 customers with all required relationships
    Customer::factory()->count(5)->create();

    $response = $this->getJson(route('customer.list'));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'cust_id',
                'customer_name',
                'cust_first_name',
                'cust_middle_name',
                'cust_last_name',
                'contact_number',
                'location',
                'land_mark',
                'created_at',
                'status',
                'status_badge',
                'resolution_no',
                'c_type',
            ],
        ],
        'current_page',
        'last_page',
        'per_page',
        'total',
        'from',
        'to',
    ]);

    expect($response->json('data'))->toHaveCount(5);
});

test('search functionality filters customers correctly', function () {
    // Create multiple customers
    Customer::factory()->count(3)->create();

    // Create a customer with a unique name for searching
    $uniqueCustomer = Customer::factory()->withName('UNIQUE', 'TESTNAME', 'MIDDLE')->create();

    $response = $this->getJson(route('customer.list', ['search' => 'UNIQUE']));

    $response->assertOk();
    $data = $response->json('data');

    expect($data)->toHaveCount(1)
        ->and($data[0]['cust_first_name'])->toBe('UNIQUE')
        ->and($data[0]['cust_last_name'])->toBe('TESTNAME');
});

test('search can find customers by last name', function () {
    Customer::factory()->count(2)->create();
    $customer = Customer::factory()->withName('JOHN', 'SEARCHABLE')->create();

    $response = $this->getJson(route('customer.list', ['search' => 'SEARCHABLE']));

    $response->assertOk();
    $data = $response->json('data');

    expect($data)->toHaveCount(1)
        ->and($data[0]['cust_last_name'])->toBe('SEARCHABLE');
});

test('search can find customers by resolution number', function () {
    $customer = Customer::factory()->create([
        'resolution_no' => 'INITAO-TEST-1234567890',
    ]);

    Customer::factory()->count(2)->create();

    $response = $this->getJson(route('customer.list', ['search' => 'TEST-1234567890']));

    $response->assertOk();
    $data = $response->json('data');

    expect($data)->toHaveCount(1)
        ->and($data[0]['resolution_no'])->toBe('INITAO-TEST-1234567890');
});

test('pagination works correctly', function () {
    // Create 15 customers
    Customer::factory()->count(15)->create();

    // Request page 1 with per_page=10
    $response = $this->getJson(route('customer.list', [
        'page' => 1,
        'per_page' => 10,
    ]));

    $response->assertOk();

    expect($response->json('data'))->toHaveCount(10)
        ->and($response->json('total'))->toBe(15)
        ->and($response->json('current_page'))->toBe(1)
        ->and($response->json('last_page'))->toBe(2)
        ->and($response->json('per_page'))->toBe(10);
});

test('pagination page 2 returns remaining records', function () {
    Customer::factory()->count(15)->create();

    // Request page 2 with per_page=10
    $response = $this->getJson(route('customer.list', [
        'page' => 2,
        'per_page' => 10,
    ]));

    $response->assertOk();

    expect($response->json('data'))->toHaveCount(5)
        ->and($response->json('current_page'))->toBe(2)
        ->and($response->json('total'))->toBe(15);
});

test('user without customers view permission cannot access customer list', function () {
    // Create unauthorized user without permission
    $unauthorizedUser = User::factory()->create();
    $this->actingAs($unauthorizedUser);

    $response = $this->get(route('customer.list'));

    $response->assertForbidden();
});

test('unauthenticated user is redirected to login', function () {
    auth()->logout();

    $response = $this->get(route('customer.list'));

    $response->assertRedirect(route('login'));
});

test('customer list returns customers with correct status badges', function () {
    // Create customers with different statuses
    Customer::factory()->create(['stat_id' => Status::getIdByDescription(Status::ACTIVE)]);
    Customer::factory()->create(['stat_id' => Status::getIdByDescription(Status::INACTIVE)]);
    Customer::factory()->create(['stat_id' => Status::getIdByDescription(Status::PENDING)]);

    $response = $this->getJson(route('customer.list'));

    $response->assertOk();
    $data = $response->json('data');

    expect($data)->toHaveCount(3);

    // Check that status badges are present
    foreach ($data as $customer) {
        expect($customer)->toHaveKey('status')
            ->and($customer)->toHaveKey('status_badge')
            ->and($customer['status_badge'])->toContain('span');
    }
});

test('customer list includes location information', function () {
    $customer = Customer::factory()->create();

    $response = $this->getJson(route('customer.list'));

    $response->assertOk();
    $data = $response->json('data');

    expect($data[0])->toHaveKey('location')
        ->and($data[0]['location'])->toBeString();
});

test('customer list supports datatables search format', function () {
    Customer::factory()->withName('DATATABLES', 'TEST')->create();
    Customer::factory()->count(2)->create();

    // DataTables format with nested search object
    $response = $this->getJson(route('customer.list', [
        'search' => [
            'value' => 'DATATABLES',
        ],
    ]));

    $response->assertOk();
    $data = $response->json('data');

    expect($data)->toHaveCount(1)
        ->and($data[0]['cust_first_name'])->toBe('DATATABLES');
});

test('customer list supports datatables draw parameter', function () {
    Customer::factory()->count(5)->create();

    // Request with draw parameter for DataTables
    $response = $this->getJson(route('customer.list', [
        'draw' => 1,
    ]));

    $response->assertOk();
    $response->assertJsonStructure([
        'draw',
        'recordsTotal',
        'recordsFiltered',
        'data',
    ]);

    expect($response->json('draw'))->toBe(1);
});

test('customer list can filter by status', function () {
    // Create customers with different statuses
    Customer::factory()->count(2)->create(['stat_id' => Status::getIdByDescription(Status::ACTIVE)]);
    Customer::factory()->count(3)->create(['stat_id' => Status::getIdByDescription(Status::INACTIVE)]);

    $response = $this->getJson(route('customer.list', [
        'status_filter' => 'ACTIVE',
    ]));

    $response->assertOk();
    $data = $response->json('data');

    expect($data)->toHaveCount(2);

    foreach ($data as $customer) {
        expect($customer['status'])->toBe('ACTIVE');
    }
});

test('customer list default sorting is by create date descending', function () {
    // Create customers with different dates
    $oldCustomer = Customer::factory()->create([
        'create_date' => now()->subDays(10),
        'cust_first_name' => 'OLD',
    ]);

    $newCustomer = Customer::factory()->create([
        'create_date' => now()->subDay(),
        'cust_first_name' => 'NEW',
    ]);

    $response = $this->getJson(route('customer.list'));

    $response->assertOk();
    $data = $response->json('data');

    // Newest should be first (descending order)
    expect($data[0]['cust_first_name'])->toBe('NEW')
        ->and($data[1]['cust_first_name'])->toBe('OLD');
});

test('customer list formats customer name correctly', function () {
    $customer = Customer::factory()->create([
        'cust_first_name' => 'JOHN',
        'cust_middle_name' => 'PAUL',
        'cust_last_name' => 'DOE',
    ]);

    $response = $this->getJson(route('customer.list'));

    $response->assertOk();
    $data = $response->json('data');

    expect($data[0]['customer_name'])->toBe('JOHN PAUL DOE');
});

test('customer list handles customers without middle name', function () {
    $customer = Customer::factory()->create([
        'cust_first_name' => 'JANE',
        'cust_middle_name' => null,
        'cust_last_name' => 'SMITH',
    ]);

    $response = $this->getJson(route('customer.list'));

    $response->assertOk();
    $data = $response->json('data');

    expect($data[0]['customer_name'])->toBe('JANE  SMITH')
        ->and($data[0]['cust_middle_name'])->toBe('');
});

test('empty customer list returns correct structure', function () {
    // Don't create any customers

    $response = $this->getJson(route('customer.list'));

    $response->assertOk();
    $response->assertJsonStructure([
        'data',
        'current_page',
        'last_page',
        'per_page',
        'total',
    ]);

    expect($response->json('data'))->toBeArray()
        ->and($response->json('data'))->toHaveCount(0)
        ->and($response->json('total'))->toBe(0);
});

test('customer list per_page parameter controls items per page', function () {
    Customer::factory()->count(20)->create();

    $response = $this->getJson(route('customer.list', [
        'per_page' => 5,
    ]));

    $response->assertOk();

    expect($response->json('data'))->toHaveCount(5)
        ->and($response->json('per_page'))->toBe(5)
        ->and($response->json('total'))->toBe(20)
        ->and($response->json('last_page'))->toBe(4);
});

test('customer list includes meter_no field in response', function () {
    $customer = Customer::factory()->create();

    $response = $this->getJson(route('customer.list'));

    $response->assertOk();
    $data = $response->json('data');

    expect($data[0])->toHaveKey('meter_no')
        ->and($data[0]['meter_no'])->toBeString();
});

test('customer list includes current_bill field in response', function () {
    $customer = Customer::factory()->create();

    $response = $this->getJson(route('customer.list'));

    $response->assertOk();
    $data = $response->json('data');

    expect($data[0])->toHaveKey('current_bill')
        ->and($data[0]['current_bill'])->toBeString()
        ->and($data[0]['current_bill'])->toContain('₱');
});

test('customer list shows meter number for customer with active meter', function () {
    $customer = Customer::factory()->create();

    // Create account_type
    $accountTypeId = \DB::table('account_type')->insertGetId([
        'at_desc' => 'RESIDENTIAL',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create active service connection
    $serviceConnection = \DB::table('ServiceConnection')->insertGetId([
        'customer_id' => $customer->cust_id,
        'address_id' => $customer->ca_id,
        'account_no' => 'ACC-TEST-001',
        'account_type_id' => $accountTypeId,
        'started_at' => now(),
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create meter
    $meter = \DB::table('Meter')->insertGetId([
        'mtr_serial' => 'MTR-FEATURE-001',
        'mtr_brand' => 'Test Brand',
        'mtr_size' => '1/2"',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create meter assignment
    \DB::table('MeterAssignment')->insert([
        'connection_id' => $serviceConnection,
        'meter_id' => $meter,
        'assignment_date' => now(),
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->getJson(route('customer.list'));

    $response->assertOk();
    $data = $response->json('data');

    expect($data[0]['meter_no'])->toBe('MTR-FEATURE-001');
});

test('customer list shows unpaid bill amount correctly', function () {
    $customer = Customer::factory()->create();

    // Create unpaid bills in CustomerLedger
    \DB::table('CustomerLedger')->insert([
        [
            'customer_id' => $customer->cust_id,
            'source_type' => 'BILL',
            'source_id' => 1,
            'txn_date' => now(),
            'debit' => 1250.50,
            'credit' => 0,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'customer_id' => $customer->cust_id,
            'source_type' => 'BILL',
            'source_id' => 2,
            'txn_date' => now(),
            'debit' => 350.00,
            'credit' => 0,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    // Create partial payment
    \DB::table('CustomerLedger')->insert([
        'customer_id' => $customer->cust_id,
        'source_type' => 'PAYMENT',
        'source_id' => 1,
        'txn_date' => now(),
        'debit' => 0,
        'credit' => 500.00,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->getJson(route('customer.list'));

    $response->assertOk();
    $data = $response->json('data');

    // Total debits: 1250.50 + 350.00 = 1600.50
    // Total credits: 500.00
    // Unpaid: 1600.50 - 500.00 = 1100.50
    expect($data[0]['current_bill'])->toBe('₱1,100.50');
});

test('customer list shows N/A meter_no when customer has no service connection', function () {
    $customer = Customer::factory()->create();

    $response = $this->getJson(route('customer.list'));

    $response->assertOk();
    $data = $response->json('data');

    expect($data[0]['meter_no'])->toBe('N/A');
});

test('customer list shows zero balance when no bills exist', function () {
    $customer = Customer::factory()->create();

    $response = $this->getJson(route('customer.list'));

    $response->assertOk();
    $data = $response->json('data');

    expect($data[0]['current_bill'])->toBe('₱0.00');
});
