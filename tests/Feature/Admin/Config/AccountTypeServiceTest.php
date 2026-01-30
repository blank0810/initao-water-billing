<?php

namespace Tests\Feature\Admin\Config;

use App\Models\AccountType;
use App\Models\Status;
use App\Services\Admin\Config\AccountTypeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTypeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AccountTypeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AccountTypeService::class);
    }

    public function test_can_get_all_account_types_with_filters()
    {
        AccountType::create([
            'at_desc' => 'Residential',
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $result = $this->service->getAllAccountTypes([
            'search' => 'Residential',
        ]);

        $this->assertArrayHasKey('data', $result);
        $this->assertCount(1, $result['data']);
    }

    public function test_can_create_account_type()
    {
        $data = ['at_desc' => 'Commercial'];

        $accountType = $this->service->createAccountType($data);

        $this->assertInstanceOf(AccountType::class, $accountType);
        $this->assertEquals('Commercial', $accountType->at_desc);
        $this->assertEquals(Status::getIdByDescription(Status::ACTIVE), $accountType->stat_id);
    }

    public function test_can_update_account_type()
    {
        $accountType = AccountType::create([
            'at_desc' => 'Old Type',
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $updated = $this->service->updateAccountType($accountType->at_id, [
            'at_desc' => 'New Type',
        ]);

        $this->assertEquals('New Type', $updated->at_desc);
    }

    public function test_cannot_delete_account_type_with_connections()
    {
        $accountType = AccountType::create([
            'at_desc' => 'Test Type',
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        // Create required dependencies (address must be created before customer)
        $province = \App\Models\Province::create([
            'prov_desc' => 'Test Province',
            'prov_code' => 'TPROV',
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $town = \App\Models\Town::create([
            't_desc' => 'Test Town',
            't_code' => 'TTOWN',
            'prov_id' => $province->prov_id,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $barangay = \App\Models\Barangay::create([
            'b_desc' => 'Test Barangay',
            'b_code' => 'TBRGY',
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $purok = \App\Models\Purok::create([
            'p_desc' => 'Test Purok',
            'b_id' => $barangay->b_id,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $address = \App\Models\ConsumerAddress::create([
            'p_id' => $purok->p_id,
            'b_id' => $barangay->b_id,
            't_id' => $town->t_id,
            'prov_id' => $province->prov_id,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $customer = \App\Models\Customer::create([
            'cust_last_name' => 'Test',
            'cust_first_name' => 'Customer',
            'cust_middle_name' => 'T',
            'resolution_no' => 'TEST-001',
            'ca_id' => $address->ca_id,
            'c_type' => 'Individual',
            'create_date' => now(),
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        // Create service connection using this account type
        \App\Models\ServiceConnection::create([
            'account_no' => 'TEST-001',
            'customer_id' => $customer->cust_id,
            'address_id' => $address->ca_id,
            'account_type_id' => $accountType->at_id,
            'started_at' => now(),
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $this->expectException(\DomainException::class);
        $this->service->deleteAccountType($accountType->at_id);
    }
}
