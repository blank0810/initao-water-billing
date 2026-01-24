<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    // Create permission if it doesn't exist
    Permission::firstOrCreate(['name' => 'customers.view']);
});

test('customer stats endpoint requires authentication', function () {
    $response = $this->getJson('/customer/stats');

    $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthenticated.']);
});

test('customer stats endpoint requires customers.view permission', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->getJson('/customer/stats');

    $response->assertStatus(403);
});

test('customer stats endpoint returns correct json structure', function () {
    $user = User::factory()->create();
    $permission = Permission::firstOrCreate(['name' => 'customers.view']);
    $user->givePermissionTo($permission);

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
    $permission = Permission::firstOrCreate(['name' => 'customers.view']);
    $user->givePermissionTo($permission);

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
    $permission = Permission::firstOrCreate(['name' => 'customers.view']);
    $user->givePermissionTo($permission);

    $response = $this->actingAs($user)
        ->getJson('/customer/stats');

    $response->assertStatus(200);

    $data = $response->json();

    expect($data['total_customers'])->toBeGreaterThanOrEqual(0);
    expect($data['residential_count'])->toBeGreaterThanOrEqual(0);
    expect($data['total_current_bill'])->toBeGreaterThanOrEqual(0);
    expect($data['overdue_count'])->toBeGreaterThanOrEqual(0);
});
