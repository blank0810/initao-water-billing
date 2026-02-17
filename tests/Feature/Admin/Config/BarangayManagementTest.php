<?php

namespace Tests\Feature\Admin\Config;

use App\Models\Barangay;
use App\Models\Permission;
use App\Models\Status;
use App\Models\User;

beforeEach(function () {
    // Create required statuses for foreign keys
    Status::create(['stat_id' => 1, 'stat_desc' => 'ACTIVE']);
    Status::create(['stat_id' => 2, 'stat_desc' => 'INACTIVE']);

    // Create required user type
    \DB::table('user_types')->insert([
        'ut_id' => 3,
        'ut_desc' => 'ADMIN',
        'stat_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->user = User::factory()->create();

    // Create and assign permission
    $permissionId = \DB::table('permissions')->insertGetId([
        'permission_name' => 'config.geographic.manage',
        'description' => 'Manage geographic configuration',
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
});

test('admin can view barangay list page', function () {
    $response = $this->get(route('config.barangays.index'));

    $response->assertOk();
    $response->assertViewIs('pages.admin.config.barangays.index');
});

test('admin can create new barangay', function () {
    $data = [
        'b_desc' => 'New Barangay',
        'b_code' => 'NB-001',
    ];

    $response = $this->postJson(route('config.barangays.store'), $data);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'message' => 'Barangay created successfully',
    ]);

    $this->assertDatabaseHas('barangay', [
        'b_desc' => 'New Barangay',
        'b_code' => 'NB-001',
    ]);
});

test('barangay name must be unique', function () {
    Barangay::factory()->create(['b_desc' => 'Poblacion']);

    $response = $this->postJson(route('config.barangays.store'), [
        'b_desc' => 'Poblacion',
        'b_code' => 'POB-001',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['b_desc']);
});

test('admin can update barangay', function () {
    $barangay = Barangay::factory()->create(['b_desc' => 'Old Name']);

    $response = $this->putJson(route('config.barangays.update', $barangay->b_id), [
        'b_desc' => 'Updated Name',
        'b_code' => 'UPD-001',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('barangay', [
        'b_id' => $barangay->b_id,
        'b_desc' => 'Updated Name',
    ]);
});

test('admin can view barangay details', function () {
    $barangay = Barangay::factory()->create();

    $response = $this->getJson(route('config.barangays.show', $barangay->b_id));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'b_id',
            'b_desc',
            'b_code',
            'puroks_count',
            'addresses_count',
        ],
    ]);
});

test('cannot delete barangay with existing puroks', function () {
    $barangay = Barangay::factory()->create();
    $barangay->puroks()->create([
        'p_desc' => 'Purok 1',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $response = $this->deleteJson(route('config.barangays.destroy', $barangay->b_id));

    $response->assertStatus(422);
    $response->assertJson(['success' => false]);
    $this->assertDatabaseHas('barangay', ['b_id' => $barangay->b_id]);
});

test('can delete barangay without dependencies', function () {
    $barangay = Barangay::factory()->create();

    $response = $this->deleteJson(route('config.barangays.destroy', $barangay->b_id));

    $response->assertOk();
    $response->assertJson(['success' => true]);
    $this->assertDatabaseMissing('barangay', ['b_id' => $barangay->b_id]);
});

test('user without permission cannot access barangay management', function () {
    $unauthorizedUser = User::factory()->create();
    $this->actingAs($unauthorizedUser);

    $response = $this->get(route('config.barangays.index'));

    $response->assertForbidden();
});
