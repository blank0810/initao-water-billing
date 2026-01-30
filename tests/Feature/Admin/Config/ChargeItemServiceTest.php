<?php

namespace Tests\Feature\Admin\Config;

use App\Models\ChargeItem;
use App\Models\Status;
use App\Services\Admin\Config\ChargeItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChargeItemServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ChargeItemService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ChargeItemService::class);
    }

    public function test_can_get_all_charge_items_with_filters()
    {
        ChargeItem::create([
            'name' => 'Connection Fee',
            'code' => 'CONN_FEE',
            'description' => 'One-time connection fee',
            'default_amount' => 500.00,
            'charge_type' => 'one_time',
            'is_taxable' => false,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $result = $this->service->getAllChargeItems([
            'search' => 'Connection',
            'charge_type' => 'one_time',
        ]);

        $this->assertArrayHasKey('data', $result);
        $this->assertCount(1, $result['data']);
    }

    public function test_can_create_charge_item()
    {
        $data = [
            'name' => 'Installation Fee',
            'code' => 'INSTALL_FEE',
            'description' => 'Installation service fee',
            'default_amount' => 1000.00,
            'charge_type' => 'one_time',
            'is_taxable' => false,
        ];

        $chargeItem = $this->service->createChargeItem($data);

        $this->assertInstanceOf(ChargeItem::class, $chargeItem);
        $this->assertEquals('Installation Fee', $chargeItem->name);
        $this->assertEquals(1000.00, (float) $chargeItem->default_amount);
    }

    public function test_can_update_charge_item()
    {
        $chargeItem = ChargeItem::create([
            'name' => 'Old Fee',
            'code' => 'OLD_FEE',
            'description' => 'Old description',
            'default_amount' => 100.00,
            'charge_type' => 'one_time',
            'is_taxable' => false,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $updated = $this->service->updateChargeItem($chargeItem->charge_item_id, [
            'name' => 'Updated Fee',
            'default_amount' => 200.00,
        ]);

        $this->assertEquals('Updated Fee', $updated->name);
        $this->assertEquals(200.00, (float) $updated->default_amount);
    }

    public function test_cannot_delete_charge_item_with_customer_charges()
    {
        $chargeItem = ChargeItem::create([
            'name' => 'Test Fee',
            'code' => 'TEST_FEE',
            'description' => 'Test fee',
            'default_amount' => 100.00,
            'charge_type' => 'one_time',
            'is_taxable' => false,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        // Create required dependencies for customer
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

        // Create customer charge using this charge item
        \App\Models\CustomerCharge::create([
            'customer_id' => $customer->cust_id,
            'charge_item_id' => $chargeItem->charge_item_id,
            'source_type' => 'ServiceApplication',
            'source_id' => 1,
            'description' => 'Test charge',
            'quantity' => 1,
            'unit_amount' => 100.00,
            'amount' => 100.00,
            'due_date' => now(),
            'stat_id' => Status::getIdByDescription(Status::PENDING),
        ]);

        $this->expectException(\DomainException::class);
        $this->service->deleteChargeItem($chargeItem->charge_item_id);
    }
}
