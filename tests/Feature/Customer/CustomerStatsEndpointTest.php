<?php

use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create required statuses
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
});

test('customer stats endpoint requires authentication', function () {
    $response = $this->getJson('/customer/stats');

    $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthenticated.']);
});

test('customer stats endpoint requires customers.view permission', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/customer/stats');

    $response->assertStatus(403);
});

test('customer stats endpoint returns correct json structure', function () {
    $user = User::factory()->create();

    // Create and assign permission using custom permission system
    $permissionId = \DB::table('permissions')->insertGetId([
        'permission_name' => 'customers.view',
        'description' => 'View customers',
    ]);

    $roleId = \DB::table('roles')->insertGetId([
        'role_name' => 'Test Viewer',
    ]);

    \DB::table('role_permissions')->insert([
        'role_id' => $roleId,
        'permission_id' => $permissionId,
    ]);

    \DB::table('user_roles')->insert([
        'user_id' => $user->id,
        'role_id' => $roleId,
    ]);

    $response = $this->actingAs($user)
        ->getJson('/customer/stats');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'total_customers',
            'residential_count',
            'total_current_bill',
            'overdue_count',
        ]);
});

test('customer stats endpoint returns valid data types', function () {
    $user = User::factory()->create();

    // Create and assign permission
    $permissionId = \DB::table('permissions')->insertGetId([
        'permission_name' => 'customers.view',
        'description' => 'View customers',
    ]);

    $roleId = \DB::table('roles')->insertGetId([
        'role_name' => 'Test Viewer 2',
    ]);

    \DB::table('role_permissions')->insert([
        'role_id' => $roleId,
        'permission_id' => $permissionId,
    ]);

    \DB::table('user_roles')->insert([
        'user_id' => $user->id,
        'role_id' => $roleId,
    ]);

    $response = $this->actingAs($user)
        ->getJson('/customer/stats');

    $response->assertStatus(200);

    $data = $response->json();

    expect($data['total_customers'])->toBeInt();
    expect($data['residential_count'])->toBeInt();
    expect($data['total_current_bill'])->toBeNumeric();
    expect($data['overdue_count'])->toBeInt();
});

test('customer stats endpoint returns non-negative values', function () {
    $user = User::factory()->create();

    // Create and assign permission
    $permissionId = \DB::table('permissions')->insertGetId([
        'permission_name' => 'customers.view',
        'description' => 'View customers',
    ]);

    $roleId = \DB::table('roles')->insertGetId([
        'role_name' => 'Test Viewer 3',
    ]);

    \DB::table('role_permissions')->insert([
        'role_id' => $roleId,
        'permission_id' => $permissionId,
    ]);

    \DB::table('user_roles')->insert([
        'user_id' => $user->id,
        'role_id' => $roleId,
    ]);

    $response = $this->actingAs($user)
        ->getJson('/customer/stats');

    $response->assertStatus(200);

    $data = $response->json();

    expect($data['total_customers'])->toBeGreaterThanOrEqual(0);
    expect($data['residential_count'])->toBeGreaterThanOrEqual(0);
    expect($data['total_current_bill'])->toBeGreaterThanOrEqual(0);
    expect($data['overdue_count'])->toBeGreaterThanOrEqual(0);
});

test('customer stats endpoint returns zero values when no data exists', function () {
    $user = User::factory()->create();

    // Create and assign permission
    $permissionId = \DB::table('permissions')->insertGetId([
        'permission_name' => 'customers.view',
        'description' => 'View customers',
    ]);

    $roleId = \DB::table('roles')->insertGetId([
        'role_name' => 'Test Viewer 4',
    ]);

    \DB::table('role_permissions')->insert([
        'role_id' => $roleId,
        'permission_id' => $permissionId,
    ]);

    \DB::table('user_roles')->insert([
        'user_id' => $user->id,
        'role_id' => $roleId,
    ]);

    $response = $this->actingAs($user)
        ->getJson('/customer/stats');

    $response->assertStatus(200);

    $data = $response->json();

    expect($data['total_customers'])->toBe(0);
    expect($data['residential_count'])->toBe(0);
    expect($data['total_current_bill'])->toBe('0.00');
    expect($data['overdue_count'])->toBe(0);
});

test('customer stats endpoint formats total_current_bill with decimal places', function () {
    $user = User::factory()->create();

    // Create and assign permission
    $permissionId = \DB::table('permissions')->insertGetId([
        'permission_name' => 'customers.view',
        'description' => 'View customers',
    ]);

    $roleId = \DB::table('roles')->insertGetId([
        'role_name' => 'Test Viewer 5',
    ]);

    \DB::table('role_permissions')->insert([
        'role_id' => $roleId,
        'permission_id' => $permissionId,
    ]);

    \DB::table('user_roles')->insert([
        'user_id' => $user->id,
        'role_id' => $roleId,
    ]);

    $response = $this->actingAs($user)
        ->getJson('/customer/stats');

    $response->assertStatus(200);

    $data = $response->json();

    // Should be a string with 2 decimal places
    expect($data['total_current_bill'])->toBeString();
    expect($data['total_current_bill'])->toMatch('/^\d+\.\d{2}$/');
});
