<?php

use App\Models\AccountType;
use App\Models\Area;
use App\Models\Customer;
use App\Models\CustomerCharge;
use App\Models\CustomerLedger;
use App\Models\ServiceApplication;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\User;
use App\Services\Charge\ApplicationChargeService;
use App\Services\ServiceConnection\ServiceConnectionService;

beforeEach(function () {
    if (! \DB::table('statuses')->where('stat_desc', 'OVERDUE')->exists()) {
        \DB::table('statuses')->insert(['stat_desc' => 'OVERDUE']);
    }

    $this->activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $this->pendingStatusId = Status::getIdByDescription(Status::PENDING);
    $this->scheduledStatusId = Status::getIdByDescription(Status::SCHEDULED);

    if (! \DB::table('user_types')->where('ut_id', 3)->exists()) {
        \DB::table('user_types')->insert([
            'ut_id' => 3,
            'ut_desc' => 'ADMIN',
            'stat_id' => $this->activeStatusId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $this->seed(\Database\Seeders\ChargeItemSeeder::class);

    $this->user = User::factory()->create(['stat_id' => $this->activeStatusId]);
    $this->customer = Customer::factory()->create(['stat_id' => $this->activeStatusId]);
    $this->accountType = AccountType::factory()->create(['stat_id' => $this->activeStatusId]);
    $this->area = Area::factory()->create(['stat_id' => $this->activeStatusId]);
});

it('transfers charge and ledger connection_id when connection is created from application', function () {
    // Create application in SCHEDULED status
    $application = ServiceApplication::create([
        'customer_id' => $this->customer->cust_id,
        'address_id' => $this->customer->ca_id,
        'application_number' => 'APP-TEST-'.uniqid(),
        'submitted_at' => now(),
        'stat_id' => $this->scheduledStatusId,
    ]);

    // Generate application charges (connection_id will be NULL)
    $chargeService = app(ApplicationChargeService::class);
    $charges = $chargeService->generateApplicationCharges($application);

    // Create ledger entries for those charges (simulating what happens at payment)
    foreach ($charges as $charge) {
        CustomerLedger::create([
            'customer_id' => $this->customer->cust_id,
            'connection_id' => null,
            'txn_date' => now()->toDateString(),
            'post_ts' => now(),
            'source_type' => 'CHARGE',
            'source_id' => $charge->charge_id,
            'source_line_no' => 1,
            'description' => $charge->description,
            'debit' => $charge->total_amount,
            'credit' => 0,
            'user_id' => $this->user->id,
            'stat_id' => $this->activeStatusId,
        ]);
    }

    // Verify charges and ledger entries have NULL connection_id before
    expect(CustomerCharge::where('application_id', $application->application_id)
        ->whereNull('connection_id')->count())->toBe($charges->count());
    expect(CustomerLedger::where('customer_id', $this->customer->cust_id)
        ->where('source_type', 'CHARGE')
        ->whereNull('connection_id')->count())->toBe($charges->count());

    // Create connection from application
    $connectionService = app(ServiceConnectionService::class);
    $connection = $connectionService->createFromApplication(
        $application,
        $this->accountType->at_id,
        $this->area->a_id
    );

    // After connection creation, charges should have connection_id
    expect(CustomerCharge::where('application_id', $application->application_id)
        ->whereNull('connection_id')->count())->toBe(0);
    expect(CustomerCharge::where('application_id', $application->application_id)
        ->where('connection_id', $connection->connection_id)->count())->toBe($charges->count());

    // Ledger entries should also have connection_id
    expect(CustomerLedger::where('customer_id', $this->customer->cust_id)
        ->where('source_type', 'CHARGE')
        ->whereNull('connection_id')->count())->toBe(0);
    expect(CustomerLedger::where('customer_id', $this->customer->cust_id)
        ->where('source_type', 'CHARGE')
        ->where('connection_id', $connection->connection_id)->count())->toBe($charges->count());
});
