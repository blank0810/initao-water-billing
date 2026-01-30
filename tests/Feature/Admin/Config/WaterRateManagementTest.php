<?php

namespace Tests\Feature\Admin\Config;

use App\Models\AccountType;
use App\Models\Status;
use App\Models\User;
use App\Models\WaterRate;

beforeEach(function () {
    // Create required statuses for foreign keys
    Status::create(['stat_id' => 1, 'stat_desc' => 'PENDING']);
    Status::create(['stat_id' => 2, 'stat_desc' => 'ACTIVE']);
    Status::create(['stat_id' => 3, 'stat_desc' => 'INACTIVE']);

    // Create required user type
    \DB::table('user_types')->insert([
        'ut_id' => 3,
        'ut_desc' => 'ADMIN',
        'stat_id' => 2,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->user = User::factory()->create();

    // Create and assign permission
    $permissionId = \DB::table('permissions')->insertGetId([
        'permission_name' => 'config.billing.manage',
        'description' => 'Manage billing configuration',
    ]);

    $roleId = \DB::table('roles')->insertGetId([
        'role_name' => 'Test Admin',
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

    $this->accountType = AccountType::factory()->create(['at_desc' => 'Residential']);
});

test('admin can view water rates page', function () {
    $response = $this->get(route('config.water-rates.index'));

    $response->assertOk();
    $response->assertViewIs('pages.admin.config.water-rates.index');
});

test('admin can create water rate tier', function () {
    $data = [
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 0,
        'range_max' => 10,
        'rate_val' => 100.00,
        'rate_inc' => 0.00,
    ];

    $response = $this->postJson(route('config.water-rates.store'), $data);

    $response->assertStatus(201);
    $response->assertJson(['success' => true]);
    $this->assertDatabaseHas('water_rates', [
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
    ]);
});

test('range max must be greater than min', function () {
    $data = [
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 20,
        'range_max' => 10,
        'rate_val' => 100.00,
        'rate_inc' => 0.00,
    ];

    $response = $this->postJson(route('config.water-rates.store'), $data);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['range_max']);
});

test('cannot create overlapping rate ranges', function () {
    // Create first tier: 0-10
    WaterRate::create([
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 0,
        'range_max' => 10,
        'rate_val' => 100.00,
        'rate_inc' => 0.00,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Try overlapping tier: 5-15
    $data = [
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 2,
        'range_min' => 5,
        'range_max' => 15,
        'rate_val' => 150.00,
        'rate_inc' => 10.00,
    ];

    $response = $this->postJson(route('config.water-rates.store'), $data);

    $response->assertStatus(422);
    $response->assertJson(['success' => false]);
});

test('can retrieve rates by account type', function () {
    WaterRate::factory()->count(4)->create(['class_id' => $this->accountType->at_id]);

    $response = $this->getJson(route('config.water-rates.index'));

    $response->assertOk();
    $response->assertJsonStructure(['data']);
});

test('can get account types', function () {
    $response = $this->getJson(route('config.water-rates.account-types'));

    $response->assertOk();
    $response->assertJsonStructure(['data' => [['at_id', 'at_desc']]]);
});
