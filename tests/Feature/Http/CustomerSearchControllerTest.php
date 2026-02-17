<?php

use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    // Create required statuses
    Status::create(['stat_id' => 1, 'stat_desc' => 'ACTIVE']);

    // Create required user type
    DB::table('user_types')->insert([
        'ut_id' => 3,
        'ut_desc' => 'ADMIN',
        'stat_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

it('requires authentication', function () {
    $this->getJson('/api/search/customers?q=test')
        ->assertStatus(401);
});

it('returns results for valid query', function () {
    $status = Status::where('stat_desc', 'ACTIVE')->first();
    $address = ConsumerAddress::factory()->create(['stat_id' => $status->stat_id]);

    Customer::factory()->create([
        'cust_first_name' => 'SearchTest',
        'cust_last_name' => 'User',
        'ca_id' => $address->ca_id,
        'stat_id' => $status->stat_id,
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/search/customers?q=SearchTest')
        ->assertOk()
        ->assertJsonCount(1);
});

it('returns empty array for short query', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/search/customers?q=a')
        ->assertOk()
        ->assertJsonCount(0);
});

it('returns empty array for missing query', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/search/customers')
        ->assertOk()
        ->assertJsonCount(0);
});
