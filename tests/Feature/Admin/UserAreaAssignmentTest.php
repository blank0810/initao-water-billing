<?php

namespace Tests\Feature\Admin;

use App\Models\Area;
use App\Models\AreaAssignment;
use App\Models\Status;
use App\Models\User;

beforeEach(function () {
    // Create required statuses
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
        'permission_name' => 'users.manage',
        'description' => 'Manage users',
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

test('meter reader gets area assigned during creation', function () {
    $area = Area::factory()->create();

    $meterReaderRoleId = \DB::table('roles')->insertGetId([
        'role_name' => 'meter_reader',
    ]);

    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'username' => 'johndoe',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'u_type' => 3,
        'status_id' => Status::getIdByDescription(Status::ACTIVE),
        'role_id' => $meterReaderRoleId,
        'meter_reader_areas' => [$area->a_id],
    ];

    $response = $this->postJson(route('user.store'), $userData);

    $response->assertStatus(201);

    $user = User::where('email', 'john@example.com')->first();

    $this->assertDatabaseHas('AreaAssignment', [
        'user_id' => $user->id,
        'area_id' => $area->a_id,
        'effective_to' => null,
    ]);
});

test('meter reader can be assigned multiple areas', function () {
    $area1 = Area::factory()->create();
    $area2 = Area::factory()->create();

    $meterReaderRoleId = \DB::table('roles')->insertGetId([
        'role_name' => 'meter_reader',
    ]);

    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'username' => 'johndoe',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'u_type' => 3,
        'status_id' => Status::getIdByDescription(Status::ACTIVE),
        'role_id' => $meterReaderRoleId,
        'meter_reader_areas' => [$area1->a_id, $area2->a_id],
    ];

    $response = $this->postJson(route('user.store'), $userData);

    $response->assertStatus(201);

    $user = User::where('email', 'john@example.com')->first();

    expect($user->areaAssignments)->toHaveCount(2);
});

test('non-meter-reader does not get area assignment', function () {
    $area = Area::factory()->create();

    $adminRoleId = \DB::table('roles')->insertGetId([
        'role_name' => 'admin',
    ]);

    $userData = [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'username' => 'adminuser',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'u_type' => 3,
        'status_id' => Status::getIdByDescription(Status::ACTIVE),
        'role_id' => $adminRoleId,
        'meter_reader_areas' => [$area->a_id], // Should be ignored
    ];

    $response = $this->postJson(route('user.store'), $userData);

    $response->assertStatus(201);

    $user = User::where('email', 'admin@example.com')->first();

    $this->assertDatabaseMissing('AreaAssignment', [
        'user_id' => $user->id,
    ]);
});

test('area assignment has correct effective date', function () {
    $area = Area::factory()->create();

    $meterReaderRoleId = \DB::table('roles')->insertGetId([
        'role_name' => 'meter_reader',
    ]);

    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'username' => 'johndoe',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'u_type' => 3,
        'status_id' => Status::getIdByDescription(Status::ACTIVE),
        'role_id' => $meterReaderRoleId,
        'meter_reader_areas' => [$area->a_id],
    ];

    $this->postJson(route('user.store'), $userData);

    $user = User::where('email', 'john@example.com')->first();
    $assignment = AreaAssignment::where('user_id', $user->id)->first();

    expect($assignment->effective_from->toDateString())->toBe(now()->toDateString())
        ->and($assignment->effective_to)->toBeNull();
});
