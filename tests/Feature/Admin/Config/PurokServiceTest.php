<?php

namespace Tests\Feature\Admin\Config;

use App\Models\Barangay;
use App\Models\Purok;
use App\Models\Status;
use App\Services\Admin\Config\PurokService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurokServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PurokService $service;
    protected Barangay $barangay;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PurokService::class);

        // Create barangay for tests
        $this->barangay = Barangay::create([
            'b_desc' => 'Test Barangay',
            'b_code' => 'TB01',
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);
    }

    public function test_can_get_all_puroks_with_filters()
    {
        Purok::create([
            'p_desc' => 'Purok 1',
            'b_id' => $this->barangay->b_id,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $result = $this->service->getAllPuroks([
            'search' => 'Purok',
            'barangay_id' => $this->barangay->b_id,
        ]);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertCount(1, $result['data']);
    }

    public function test_can_create_purok()
    {
        $data = [
            'p_desc' => 'New Purok',
            'b_id' => $this->barangay->b_id,
        ];

        $purok = $this->service->createPurok($data);

        $this->assertInstanceOf(Purok::class, $purok);
        $this->assertEquals('New Purok', $purok->p_desc);
        $this->assertEquals(Status::getIdByDescription(Status::ACTIVE), $purok->stat_id);
    }

    public function test_can_update_purok()
    {
        $purok = Purok::create([
            'p_desc' => 'Old Name',
            'b_id' => $this->barangay->b_id,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $updated = $this->service->updatePurok($purok->p_id, [
            'p_desc' => 'New Name',
        ]);

        $this->assertEquals('New Name', $updated->p_desc);
    }

    public function test_cannot_delete_purok_with_addresses()
    {
        $purok = Purok::create([
            'p_desc' => 'Test Purok',
            'b_id' => $this->barangay->b_id,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        // Create required town and province first
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

        // Create address using this purok
        \App\Models\ConsumerAddress::create([
            'p_id' => $purok->p_id,
            'b_id' => $this->barangay->b_id,
            't_id' => $town->t_id,
            'prov_id' => $province->prov_id,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $this->expectException(\DomainException::class);
        $this->service->deletePurok($purok->p_id);
    }
}
