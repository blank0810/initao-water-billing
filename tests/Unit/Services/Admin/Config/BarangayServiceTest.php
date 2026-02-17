<?php

namespace Tests\Unit\Services\Admin\Config;

use App\Models\Barangay;
use App\Models\Status;
use App\Services\Admin\Config\BarangayService;

beforeEach(function () {
    $this->service = new BarangayService;
});

test('getAllBarangays returns paginated results', function () {
    Barangay::factory()->count(5)->create();

    $result = $this->service->getAllBarangays([]);

    expect($result)->toHaveKey('data')
        ->and($result['data'])->toHaveCount(5);
});

test('getAllBarangays filters by search term', function () {
    Barangay::factory()->create(['b_desc' => 'Poblacion']);
    Barangay::factory()->create(['b_desc' => 'Kanitoan']);

    $result = $this->service->getAllBarangays(['search' => 'Poblacion']);

    expect($result['data'])->toHaveCount(1)
        ->and($result['data'][0]['b_desc'])->toBe('Poblacion');
});

test('createBarangay sets active status by default', function () {
    $barangay = $this->service->createBarangay([
        'b_desc' => 'Test Barangay',
        'b_code' => 'TB-001',
    ]);

    expect($barangay->stat_id)->toBe(Status::getIdByDescription(Status::ACTIVE))
        ->and($barangay->b_desc)->toBe('Test Barangay')
        ->and($barangay->b_code)->toBe('TB-001');
});

test('updateBarangay updates barangay fields', function () {
    $barangay = Barangay::factory()->create(['b_desc' => 'Old Name']);

    $updated = $this->service->updateBarangay($barangay->b_id, [
        'b_desc' => 'New Name',
        'b_code' => 'NEW-001',
    ]);

    expect($updated->b_desc)->toBe('New Name')
        ->and($updated->b_code)->toBe('NEW-001');
});

test('deleteBarangay throws exception when puroks exist', function () {
    $barangay = Barangay::factory()->create();
    $barangay->puroks()->create([
        'p_desc' => 'Purok 1',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    expect(fn () => $this->service->deleteBarangay($barangay->b_id))
        ->toThrow(\DomainException::class, 'Cannot delete barangay');
});

test('deleteBarangay throws exception when consumer addresses exist', function () {
    $barangay = Barangay::factory()->create();

    // Create required related records for foreign keys
    $purok = \App\Models\Purok::create([
        'b_id' => $barangay->b_id,
        'p_desc' => 'Test Purok',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $province = \App\Models\Province::create([
        'prov_desc' => 'Test Province',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $town = \App\Models\Town::create([
        'prov_id' => $province->prov_id,
        't_desc' => 'Test Town',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    \App\Models\ConsumerAddress::create([
        'b_id' => $barangay->b_id,
        'p_id' => $purok->p_id,
        't_id' => $town->t_id,
        'prov_id' => $province->prov_id,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    expect(fn () => $this->service->deleteBarangay($barangay->b_id))
        ->toThrow(\DomainException::class, 'Cannot delete barangay');
});

test('deleteBarangay succeeds when no dependencies', function () {
    $barangay = Barangay::factory()->create();

    $this->service->deleteBarangay($barangay->b_id);

    expect(Barangay::find($barangay->b_id))->toBeNull();
});

test('getBarangayDetails returns barangay with relationships', function () {
    $barangay = Barangay::factory()->create();

    $details = $this->service->getBarangayDetails($barangay->b_id);

    expect($details)->toHaveKey('puroks_count')
        ->and($details)->toHaveKey('addresses_count');
});
