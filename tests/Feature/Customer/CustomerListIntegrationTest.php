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

/**
 * Integration Test 1: Complete Page Load Flow
 * Tests: Page Load → Stats Load → Table Load
 */
test('complete page load flow works end to end', function () {
    // Create test data
    Customer::factory()->count(15)->create();

    // Step 1: Load the page
    $pageResponse = $this->get(route('customer.list'));
    $pageResponse->assertOk();
    $pageResponse->assertViewIs('pages.customer.customer-list');

    // Step 2: Verify stats API works
    $statsResponse = $this->getJson(route('customer.stats'));
    $statsResponse->assertOk();
    $statsResponse->assertJsonStructure([
        'total_customers',
        'residential_count',
        'total_current_bill',
        'overdue_count',
    ]);

    expect($statsResponse->json('total_customers'))->toBe(15);

    // Step 3: Verify table API works
    $tableResponse = $this->getJson(route('customer.list'));
    $tableResponse->assertOk();
    $tableResponse->assertJsonStructure([
        'data' => [
            '*' => [
                'cust_id',
                'customer_name',
                'location',
                'meter_no',
                'current_bill',
                'status',
            ],
        ],
        'current_page',
        'last_page',
        'per_page',
        'total',
    ]);

    expect($tableResponse->json('total'))->toBe(15)
        ->and($tableResponse->json('data'))->toHaveCount(10); // Default per_page
});

/**
 * Integration Test 2: Search Workflow with Pagination
 * Tests: Enter search → Table updates → Pagination preserves search
 */
test('search workflow with pagination preserves state', function () {
    // Create customers with specific names for searching
    Customer::factory()->count(5)->create(); // Other customers
    Customer::factory()->count(15)->withName('JOHN', 'SMITH')->create(); // Searchable customers

    // Step 1: Search for "JOHN"
    $searchResponse = $this->getJson(route('customer.list', ['search' => 'JOHN']));
    $searchResponse->assertOk();

    $data = $searchResponse->json('data');
    expect($searchResponse->json('total'))->toBe(15)
        ->and($data)->toHaveCount(10); // First page, 10 per page

    // Verify all results contain search term
    foreach ($data as $customer) {
        expect($customer['cust_first_name'])->toBe('JOHN');
    }

    // Step 2: Navigate to page 2 with search
    $page2Response = $this->getJson(route('customer.list', [
        'search' => 'JOHN',
        'page' => 2,
    ]));
    $page2Response->assertOk();

    expect($page2Response->json('current_page'))->toBe(2)
        ->and($page2Response->json('total'))->toBe(15)
        ->and($page2Response->json('data'))->toHaveCount(5); // Remaining 5 customers

    // Verify search still applies
    foreach ($page2Response->json('data') as $customer) {
        expect($customer['cust_first_name'])->toBe('JOHN');
    }
});

/**
 * Integration Test 3: Filter Workflow with Pagination
 * Tests: Select filter → Table updates → Pagination preserves filter
 */
test('filter workflow with pagination preserves state', function () {
    // Create customers with different statuses
    Customer::factory()->count(12)->create(['stat_id' => Status::getIdByDescription(Status::ACTIVE)]);
    Customer::factory()->count(8)->create(['stat_id' => Status::getIdByDescription(Status::INACTIVE)]);

    // Step 1: Filter by ACTIVE status
    $filterResponse = $this->getJson(route('customer.list', ['status_filter' => 'ACTIVE']));
    $filterResponse->assertOk();

    expect($filterResponse->json('total'))->toBe(12)
        ->and($filterResponse->json('data'))->toHaveCount(10); // First page

    // Verify all results are ACTIVE
    foreach ($filterResponse->json('data') as $customer) {
        expect($customer['status'])->toBe('ACTIVE');
    }

    // Step 2: Navigate to page 2 with filter
    $page2Response = $this->getJson(route('customer.list', [
        'status_filter' => 'ACTIVE',
        'page' => 2,
    ]));
    $page2Response->assertOk();

    expect($page2Response->json('current_page'))->toBe(2)
        ->and($page2Response->json('total'))->toBe(12)
        ->and($page2Response->json('data'))->toHaveCount(2); // Remaining 2 customers

    // Verify filter still applies
    foreach ($page2Response->json('data') as $customer) {
        expect($customer['status'])->toBe('ACTIVE');
    }
});

/**
 * Integration Test 4: Combined Search and Filter
 * Tests: Search + Filter work together correctly
 */
test('combined search and filter work together', function () {
    // Create customers with different combinations
    Customer::factory()->count(5)->withName('JOHN', 'DOE')->create(['stat_id' => Status::getIdByDescription(Status::ACTIVE)]);
    Customer::factory()->count(3)->withName('JOHN', 'SMITH')->create(['stat_id' => Status::getIdByDescription(Status::INACTIVE)]);
    Customer::factory()->count(2)->withName('JANE', 'DOE')->create(['stat_id' => Status::getIdByDescription(Status::ACTIVE)]);

    // Step 1: Apply both search and filter
    $response = $this->getJson(route('customer.list', [
        'search' => 'JOHN',
        'status_filter' => 'ACTIVE',
    ]));
    $response->assertOk();

    // Should only return JOHN + ACTIVE = 5 customers
    expect($response->json('total'))->toBe(5);

    $data = $response->json('data');
    foreach ($data as $customer) {
        expect($customer['cust_first_name'])->toBe('JOHN')
            ->and($customer['status'])->toBe('ACTIVE');
    }

    // Step 2: Change search but keep filter
    $response2 = $this->getJson(route('customer.list', [
        'search' => 'JANE',
        'status_filter' => 'ACTIVE',
    ]));
    $response2->assertOk();

    expect($response2->json('total'))->toBe(2);

    foreach ($response2->json('data') as $customer) {
        expect($customer['cust_first_name'])->toBe('JANE')
            ->and($customer['status'])->toBe('ACTIVE');
    }
});

/**
 * Integration Test 5: Pagination Workflow
 * Tests: Page size change, page navigation, state preservation
 */
test('pagination workflow works correctly', function () {
    // Create 30 customers
    Customer::factory()->count(30)->create();

    // Step 1: Default pagination (10 per page)
    $response1 = $this->getJson(route('customer.list'));
    expect($response1->json('per_page'))->toBe(10)
        ->and($response1->json('total'))->toBe(30)
        ->and($response1->json('last_page'))->toBe(3)
        ->and($response1->json('data'))->toHaveCount(10);

    // Step 2: Change page size to 25
    $response2 = $this->getJson(route('customer.list', ['per_page' => 25]));
    expect($response2->json('per_page'))->toBe(25)
        ->and($response2->json('total'))->toBe(30)
        ->and($response2->json('last_page'))->toBe(2)
        ->and($response2->json('data'))->toHaveCount(25);

    // Step 3: Navigate to page 2 with per_page=25
    $response3 = $this->getJson(route('customer.list', [
        'per_page' => 25,
        'page' => 2,
    ]));
    expect($response3->json('current_page'))->toBe(2)
        ->and($response3->json('per_page'))->toBe(25)
        ->and($response3->json('data'))->toHaveCount(5); // Remaining 5

    // Step 4: Pagination with search
    Customer::factory()->count(20)->withName('SEARCH', 'TEST')->create();

    $response4 = $this->getJson(route('customer.list', [
        'search' => 'SEARCH',
        'per_page' => 10,
        'page' => 2,
    ]));
    expect($response4->json('current_page'))->toBe(2)
        ->and($response4->json('total'))->toBe(20)
        ->and($response4->json('data'))->toHaveCount(10);

    foreach ($response4->json('data') as $customer) {
        expect($customer['cust_first_name'])->toBe('SEARCH');
    }
});

/**
 * Integration Test 6: API Response Structure Validation
 * Tests: All required fields are present and correctly formatted
 */
test('api responses include all required fields', function () {
    $customer = Customer::factory()->create();

    $response = $this->getJson(route('customer.list'));
    $response->assertOk();

    $data = $response->json('data')[0];

    // Verify all required fields are present
    expect($data)->toHaveKeys([
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
        'meter_no',
        'current_bill',
    ]);

    // Verify field types
    expect($data['cust_id'])->toBeInt()
        ->and($data['customer_name'])->toBeString()
        ->and($data['location'])->toBeString()
        ->and($data['meter_no'])->toBeString()
        ->and($data['current_bill'])->toBeString()
        ->and($data['status'])->toBeString()
        ->and($data['status_badge'])->toBeString();

    // Verify formatting
    expect($data['current_bill'])->toContain('₱')
        ->and($data['status_badge'])->toContain('span');
});

/**
 * Integration Test 7: Pagination Metadata Correctness
 * Tests: Pagination metadata matches actual data
 */
test('pagination metadata is correct', function () {
    Customer::factory()->count(37)->create();

    $response = $this->getJson(route('customer.list', ['per_page' => 10]));
    $response->assertOk();

    $json = $response->json();

    // Verify pagination metadata
    expect($json['total'])->toBe(37)
        ->and($json['per_page'])->toBe(10)
        ->and($json['current_page'])->toBe(1)
        ->and($json['last_page'])->toBe(4)
        ->and($json['from'])->toBe(1)
        ->and($json['to'])->toBe(10);

    // Verify data count matches metadata
    expect($json['data'])->toHaveCount(10);

    // Check last page
    $lastPageResponse = $this->getJson(route('customer.list', [
        'per_page' => 10,
        'page' => 4,
    ]));

    $lastPageJson = $lastPageResponse->json();
    expect($lastPageJson['current_page'])->toBe(4)
        ->and($lastPageJson['data'])->toHaveCount(7) // 37 % 10 = 7
        ->and($lastPageJson['from'])->toBe(31)
        ->and($lastPageJson['to'])->toBe(37);
});

/**
 * Integration Test 8: Search Parameter Handling
 * Tests: Different search formats are handled correctly
 */
test('search parameter is passed correctly in different formats', function () {
    Customer::factory()->count(5)->withName('DIRECT', 'SEARCH')->create();

    // Test 1: Direct search parameter
    $response1 = $this->getJson(route('customer.list', ['search' => 'DIRECT']));
    expect($response1->json('total'))->toBe(5);

    // Test 2: DataTables format (nested search object)
    $response2 = $this->getJson(route('customer.list', [
        'search' => ['value' => 'DIRECT'],
    ]));
    expect($response2->json('total'))->toBe(5);

    // Test 3: Search by resolution number
    $customer = Customer::factory()->create(['resolution_no' => 'INITAO-XYZ-9999999999']);
    $response3 = $this->getJson(route('customer.list', ['search' => 'XYZ-9999999999']));
    expect($response3->json('total'))->toBe(1)
        ->and($response3->json('data')[0]['resolution_no'])->toBe('INITAO-XYZ-9999999999');

    // Test 4: Empty search returns all
    $response4 = $this->getJson(route('customer.list', ['search' => '']));
    expect($response4->json('total'))->toBe(6); // 5 + 1
});

/**
 * Integration Test 9: Filter Parameter Handling
 * Tests: Different filter values work correctly
 */
test('filter parameter is passed correctly', function () {
    Customer::factory()->count(3)->create(['stat_id' => Status::getIdByDescription(Status::ACTIVE)]);
    Customer::factory()->count(2)->create(['stat_id' => Status::getIdByDescription(Status::INACTIVE)]);
    Customer::factory()->count(1)->create(['stat_id' => Status::getIdByDescription(Status::PENDING)]);

    // Test 1: Filter by ACTIVE
    $response1 = $this->getJson(route('customer.list', ['status_filter' => 'ACTIVE']));
    expect($response1->json('total'))->toBe(3);

    // Test 2: Filter by INACTIVE
    $response2 = $this->getJson(route('customer.list', ['status_filter' => 'INACTIVE']));
    expect($response2->json('total'))->toBe(2);

    // Test 3: Filter by PENDING
    $response3 = $this->getJson(route('customer.list', ['status_filter' => 'PENDING']));
    expect($response3->json('total'))->toBe(1);

    // Test 4: No filter returns all
    $response4 = $this->getJson(route('customer.list'));
    expect($response4->json('total'))->toBe(6);
});

/**
 * Integration Test 10: Performance with Large Dataset
 * Tests: Page loads quickly even with 100+ customers
 */
test('performance with large dataset is acceptable', function () {
    // Create 150 customers for performance testing
    Customer::factory()->count(150)->create();

    // Measure stats API performance
    $statsStart = microtime(true);
    $statsResponse = $this->getJson(route('customer.stats'));
    $statsTime = microtime(true) - $statsStart;

    $statsResponse->assertOk();
    expect($statsTime)->toBeLessThan(1.0); // Should complete in < 1 second

    // Measure table API performance
    $tableStart = microtime(true);
    $tableResponse = $this->getJson(route('customer.list'));
    $tableTime = microtime(true) - $tableStart;

    $tableResponse->assertOk();
    expect($tableTime)->toBeLessThan(2.0); // Should complete in < 2 seconds

    // Verify correct data is returned
    expect($tableResponse->json('total'))->toBe(150)
        ->and($tableResponse->json('data'))->toHaveCount(10);

    // Measure search performance
    $searchStart = microtime(true);
    $searchResponse = $this->getJson(route('customer.list', ['search' => 'TEST']));
    $searchTime = microtime(true) - $searchStart;

    $searchResponse->assertOk();
    expect($searchTime)->toBeLessThan(2.0); // Should complete in < 2 seconds
});

/**
 * Integration Test 11: API Headers
 * Tests: Correct headers are sent and received
 */
test('api calls have correct headers', function () {
    Customer::factory()->count(5)->create();

    // Test with JSON headers
    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'X-Requested-With' => 'XMLHttpRequest',
    ])->get(route('customer.list'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/json');
    $response->assertJson(['data' => []]);
});

/**
 * Integration Test 12: Error Handling
 * Tests: Proper error responses for edge cases
 */
test('api handles edge cases correctly', function () {
    // Test 1: Invalid page number (returns empty page)
    $response1 = $this->getJson(route('customer.list', ['page' => 999]));
    $response1->assertOk();
    expect($response1->json('data'))->toBeArray();

    // Test 2: Invalid per_page value (should use default or handle gracefully)
    $response2 = $this->getJson(route('customer.list', ['per_page' => -1]));
    $response2->assertOk();

    // Test 3: Very long search string
    $longSearch = str_repeat('A', 500);
    $response3 = $this->getJson(route('customer.list', ['search' => $longSearch]));
    $response3->assertOk();
    expect($response3->json('data'))->toBeArray();

    // Test 4: Special characters in search
    Customer::factory()->create(['cust_first_name' => "O'BRIEN"]);
    $response4 = $this->getJson(route('customer.list', ['search' => "O'BRIEN"]));
    $response4->assertOk();
});

/**
 * Integration Test 13: Authentication and Authorization
 * Tests: Proper access control
 */
test('api enforces authentication and authorization', function () {
    // Test 1: Unauthenticated user
    auth()->logout();
    $response1 = $this->getJson(route('customer.list'));
    $response1->assertUnauthorized();

    // Test 2: Authenticated but no permission
    $unauthorizedUser = User::factory()->create();
    $this->actingAs($unauthorizedUser);
    $response2 = $this->get(route('customer.list'));
    $response2->assertForbidden();

    // Test 3: With correct permission
    $permissionId = \DB::table('permissions')->insertGetId([
        'permission_name' => 'customers.view',
        'description' => 'View customers',
    ]);

    $roleId = \DB::table('roles')->insertGetId([
        'role_name' => 'Authorized Viewer',
    ]);

    \DB::table('role_permissions')->insert([
        'role_id' => $roleId,
        'permission_id' => $permissionId,
    ]);

    \DB::table('user_roles')->insert([
        'user_id' => $unauthorizedUser->id,
        'role_id' => $roleId,
    ]);

    $response3 = $this->actingAs($unauthorizedUser)->get(route('customer.list'));
    $response3->assertOk();
});

/**
 * Integration Test 14: Data Consistency
 * Tests: Stats and table data are consistent
 */
test('stats and table data are consistent', function () {
    // Create customers with known data
    Customer::factory()->count(10)->create(['stat_id' => Status::getIdByDescription(Status::ACTIVE)]);
    Customer::factory()->count(5)->create(['stat_id' => Status::getIdByDescription(Status::INACTIVE)]);

    // Get stats
    $statsResponse = $this->getJson(route('customer.stats'));
    $stats = $statsResponse->json();

    // Get all customers (no filters)
    $allResponse = $this->getJson(route('customer.list', ['per_page' => 100]));
    $allCustomers = $allResponse->json();

    // Verify consistency
    expect($stats['total_customers'])->toBe($allCustomers['total'])
        ->and($allCustomers['total'])->toBe(15);

    // Count active customers from table
    $activeCount = collect($allCustomers['data'])->where('status', 'ACTIVE')->count();
    expect($activeCount)->toBe(10);
});

/**
 * Integration Test 15: Sorting
 * Tests: Default sorting and custom sorting work correctly
 */
test('default sorting is by create date descending', function () {
    // Create customers with different dates
    $oldCustomer = Customer::factory()->create([
        'create_date' => now()->subDays(10),
        'cust_first_name' => 'OLD',
    ]);

    $middleCustomer = Customer::factory()->create([
        'create_date' => now()->subDays(5),
        'cust_first_name' => 'MIDDLE',
    ]);

    $newCustomer = Customer::factory()->create([
        'create_date' => now()->subDay(),
        'cust_first_name' => 'NEW',
    ]);

    $response = $this->getJson(route('customer.list'));
    $response->assertOk();

    $data = $response->json('data');

    // Should be sorted newest first (descending)
    expect($data[0]['cust_first_name'])->toBe('NEW')
        ->and($data[1]['cust_first_name'])->toBe('MIDDLE')
        ->and($data[2]['cust_first_name'])->toBe('OLD');
});
