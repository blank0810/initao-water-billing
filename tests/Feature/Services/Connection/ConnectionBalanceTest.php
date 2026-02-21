<?php

use App\Models\AccountType;
use App\Models\Area;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\User;
use App\Services\ServiceConnection\ServiceConnectionService;

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

    $this->user = User::factory()->create(['stat_id' => $this->activeStatusId]);
    $this->customer = Customer::factory()->create(['stat_id' => $this->activeStatusId]);
    $this->accountType = AccountType::factory()->create(['stat_id' => $this->activeStatusId]);
    $this->area = Area::factory()->create(['stat_id' => $this->activeStatusId]);

    $this->connection = ServiceConnection::factory()->create([
        'customer_id' => $this->customer->cust_id,
        'address_id' => $this->customer->ca_id,
        'account_type_id' => $this->accountType->at_id,
        'area_id' => $this->area->a_id,
        'stat_id' => $this->activeStatusId,
    ]);
});

it('includes application fee charges in connection balance', function () {
    // Application fee charge
    CustomerLedger::create([
        'customer_id' => $this->customer->cust_id,
        'connection_id' => $this->connection->connection_id,
        'txn_date' => now()->subDays(10)->toDateString(),
        'post_ts' => now()->subDays(10),
        'source_type' => 'CHARGE',
        'source_id' => 1,
        'source_line_no' => 1,
        'description' => 'Connection Fee',
        'debit' => 500.00,
        'credit' => 0,
        'user_id' => $this->user->id,
        'stat_id' => $this->activeStatusId,
    ]);

    // Water bill
    CustomerLedger::create([
        'customer_id' => $this->customer->cust_id,
        'connection_id' => $this->connection->connection_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'BILL',
        'source_id' => 1,
        'source_line_no' => 1,
        'description' => 'Water Bill',
        'debit' => 1000.00,
        'credit' => 0,
        'user_id' => $this->user->id,
        'stat_id' => $this->activeStatusId,
    ]);

    // Payment
    CustomerLedger::create([
        'customer_id' => $this->customer->cust_id,
        'connection_id' => $this->connection->connection_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'PAYMENT',
        'source_id' => 1,
        'source_line_no' => 1,
        'description' => 'Payment',
        'debit' => 0,
        'credit' => 500.00,
        'user_id' => $this->user->id,
        'stat_id' => $this->activeStatusId,
    ]);

    $service = app(ServiceConnectionService::class);
    $balance = $service->getConnectionBalance($this->connection->connection_id);

    expect($balance['total_bills'])->toBe(1000.0);
    expect($balance['total_charges'])->toBe(500.0);
    expect($balance['total_payments'])->toBe(500.0);
    expect($balance['balance'])->toBe(1000.0); // 1000 + 500 - 500
});
