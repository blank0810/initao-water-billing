<?php

use App\Models\AccountType;
use App\Models\Area;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\User;

beforeEach(function () {
    if (! \DB::table('statuses')->where('stat_desc', 'OVERDUE')->exists()) {
        \DB::table('statuses')->insert(['stat_desc' => 'OVERDUE']);
    }

    $this->activeStatusId = Status::getIdByDescription(Status::ACTIVE);

    if (! \DB::table('user_types')->where('ut_id', 3)->exists()) {
        \DB::table('user_types')->insert([
            'ut_id' => 3,
            'ut_desc' => 'ADMIN',
            'stat_id' => $this->activeStatusId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $this->seed(\Database\Seeders\RoleSeeder::class);
    $this->seed(\Database\Seeders\PermissionSeeder::class);
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    $this->customer = Customer::factory()->create(['stat_id' => $this->activeStatusId]);
    $this->accountType = AccountType::factory()->create(['stat_id' => $this->activeStatusId]);
    $this->area = Area::factory()->create(['stat_id' => $this->activeStatusId]);

    $this->user = User::factory()->create(['stat_id' => $this->activeStatusId]);

    $this->connection = ServiceConnection::factory()->create([
        'customer_id' => $this->customer->cust_id,
        'address_id' => $this->customer->ca_id,
        'account_type_id' => $this->accountType->at_id,
        'area_id' => $this->area->a_id,
        'stat_id' => $this->activeStatusId,
    ]);

    // Create some ledger entries
    CustomerLedger::create([
        'customer_id' => $this->customer->cust_id,
        'connection_id' => $this->connection->connection_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'BILL',
        'source_id' => 1,
        'source_line_no' => 1,
        'description' => 'Water Bill - Test',
        'debit' => 500.00,
        'credit' => 0,
        'user_id' => $this->user->id,
        'stat_id' => $this->activeStatusId,
    ]);

    CustomerLedger::create([
        'customer_id' => $this->customer->cust_id,
        'connection_id' => $this->connection->connection_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'PAYMENT',
        'source_id' => 1,
        'source_line_no' => 1,
        'description' => 'Payment for Water Bill',
        'debit' => 0,
        'credit' => 300.00,
        'user_id' => $this->user->id,
        'stat_id' => $this->activeStatusId,
    ]);

    // Assign super_admin role
    $superAdminRole = \App\Models\Role::findByName('super_admin');
    if ($superAdminRole) {
        \DB::table('user_roles')->insert([
            'user_id' => $this->user->id,
            'role_id' => $superAdminRole->role_id,
        ]);
    }
});

it('returns paginated connection ledger data via API', function () {
    $response = $this->actingAs($this->user)
        ->getJson("/customer/service-connection/{$this->connection->connection_id}/ledger");

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'data' => [
                'entries',
                'pagination' => ['current_page', 'per_page', 'total', 'last_page'],
                'summary' => ['total_debit', 'total_credit', 'net_balance'],
            ],
        ]);

    $data = $response->json('data');
    expect($data['entries'])->toHaveCount(2);
    expect((float) $data['summary']['total_debit'])->toBe(500.0);
    expect((float) $data['summary']['total_credit'])->toBe(300.0);
    expect((float) $data['summary']['net_balance'])->toBe(200.0);
});

it('filters connection ledger by source type', function () {
    $response = $this->actingAs($this->user)
        ->getJson("/customer/service-connection/{$this->connection->connection_id}/ledger?source_type=BILL");

    $response->assertOk();
    $data = $response->json('data');
    expect($data['entries'])->toHaveCount(1);
    expect($data['entries'][0]['source_type'])->toBe('BILL');
});
