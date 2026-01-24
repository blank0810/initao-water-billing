<?php

namespace Tests\Feature\Admin\Config;

use App\Models\Area;
use App\Models\AreaAssignment;
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

test('admin can create new area', function () {
    $data = [
        'a_desc' => 'Downtown Area',
    ];

    $response = $this->postJson(route('config.areas.store'), $data);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'message' => 'Area created successfully',
    ]);

    $this->assertDatabaseHas('area', [
        'a_desc' => 'Downtown Area',
        'stat_id' => 1, // Active status
    ]);
});

test('admin cannot create duplicate area', function () {
    Area::factory()->create(['a_desc' => 'Existing Area']);

    $data = [
        'a_desc' => 'Existing Area',
    ];

    $response = $this->postJson(route('config.areas.store'), $data);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['a_desc']);
});

test('admin can update existing area', function () {
    $area = Area::factory()->create(['a_desc' => 'Old Name']);

    $data = [
        'a_desc' => 'New Name',
        'stat_id' => 2, // Inactive
    ];

    $response = $this->putJson(route('config.areas.update', $area->a_id), $data);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Area updated successfully',
    ]);

    $this->assertDatabaseHas('area', [
        'a_id' => $area->a_id,
        'a_desc' => 'New Name',
        'stat_id' => 2,
    ]);
});

test('admin can view area details', function () {
    $area = Area::factory()->create(['a_desc' => 'Test Area']);
    $anotherUser = User::factory()->create();

    AreaAssignment::create([
        'user_id' => $anotherUser->id,
        'area_id' => $area->a_id,
        'effective_from' => now(),
        'effective_to' => null,
    ]);

    $response = $this->getJson(route('config.areas.show', $area->a_id));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    expect($response->json('data.a_desc'))->toBe('Test Area')
        ->and($response->json('data.active_assignments'))->toHaveCount(1);
});

test('admin can delete area without dependencies', function () {
    $area = Area::factory()->create();

    $response = $this->deleteJson(route('config.areas.destroy', $area->a_id));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Area deleted successfully',
    ]);

    $this->assertDatabaseMissing('area', [
        'a_id' => $area->a_id,
    ]);
});

test('admin cannot delete area with active assignments', function () {
    $area = Area::factory()->create();
    $anotherUser = User::factory()->create();

    AreaAssignment::create([
        'user_id' => $anotherUser->id,
        'area_id' => $area->a_id,
        'effective_from' => now(),
        'effective_to' => null,
    ]);

    $response = $this->deleteJson(route('config.areas.destroy', $area->a_id));

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
    ]);

    expect($response->json('message'))->toContain('active meter reader assignments');

    $this->assertDatabaseHas('area', [
        'a_id' => $area->a_id,
    ]);
});

test('unauthorized user cannot access area management', function () {
    // Create a user without permission
    $unauthorizedUser = User::factory()->create();
    $this->actingAs($unauthorizedUser);

    $response = $this->postJson(route('config.areas.store'), [
        'a_desc' => 'Test Area',
    ]);

    $response->assertStatus(403);
});
