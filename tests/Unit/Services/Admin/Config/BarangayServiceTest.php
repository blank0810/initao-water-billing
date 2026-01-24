<?php

namespace Tests\Unit\Services\Admin\Config;

use App\Models\Barangay;
use App\Models\Status;
use App\Services\Admin\Config\BarangayService;

beforeEach(function () {
    $this->service = new BarangayService();
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
