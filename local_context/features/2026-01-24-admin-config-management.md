# Admin Configuration Management System - Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Build a complete admin UI for managing barangays, areas, and water rates with RBAC, plus area assignment during meter reader user creation.

**Architecture:** Service-layer pattern with Controllers (orchestration) → Services (business logic) → Models (data). Modular Blade components for UI reusability. Alpine.js for interactivity. Two new permissions: `config.geographic.manage` and `config.billing.manage`.

**Tech Stack:** Laravel 12, Pest PHP, Alpine.js, Tailwind CSS, Flowbite, MySQL 8, Docker

---

## Phase 1: Database & Permissions Setup

> **Note:** The barangay table already has all required columns (`b_id`, `b_desc`, `b_code`, `stat_id`, `created_at`, `updated_at`) from existing migrations. No new migrations needed!

### Task 1.1: Update Permission Seeder

**Files:**
- Modify: `database/seeders/PermissionSeeder.php`

**Step 1: Add new permissions to seeder**

Add these permissions to the `$permissions` array:

```php
// Configuration Management Permissions
[
    'permission_name' => 'config.geographic.manage',
    'permission_description' => 'Manage geographic configuration (barangays, areas)',
    'permission_group' => 'Configuration',
],
[
    'permission_name' => 'config.billing.manage',
    'permission_description' => 'Manage billing configuration (water rates)',
    'permission_group' => 'Configuration',
],
```

**Step 2: Run seeder**

```bash
docker compose exec water_billing_app php artisan db:seed --class=PermissionSeeder
```

Expected: "Database seeding completed successfully"

**Step 3: Verify permissions created**

```bash
docker compose exec water_billing_app php artisan tinker
```

In tinker:
```php
Permission::where('permission_group', 'Configuration')->get(['permission_name', 'permission_description']);
```

Expected: 2 permissions returned

**Step 4: Commit**

```bash
git add database/seeders/PermissionSeeder.php
git commit -m "feat(config): add configuration management permissions"
```

---

### Task 1.2: Update Role Permission Seeder

**Files:**
- Modify: `database/seeders/RolePermissionSeeder.php`

**Step 1: Add permissions to Admin role**

In the Admin role section, add the new permissions:

```php
$admin = Role::where('role_name', Role::ADMIN)->first();
$adminPermissions = Permission::whereIn('permission_name', [
    'users.view',
    'users.manage',
    'customers.view',
    'customers.manage',
    'billing.view',
    'settings.manage',
    'config.geographic.manage',  // NEW
    'config.billing.manage',      // NEW
])->pluck('permission_id')->toArray();

$admin->permissions()->sync($adminPermissions);
```

**Step 2: Run seeder**

```bash
docker compose exec water_billing_app php artisan db:seed --class=RolePermissionSeeder
```

**Step 3: Verify role has permissions**

```bash
docker compose exec water_billing_app php artisan tinker
```

In tinker:
```php
$admin = Role::where('role_name', 'admin')->first();
$admin->permissions()->where('permission_group', 'Configuration')->get(['permission_name']);
```

Expected: 2 configuration permissions returned

**Step 4: Commit**

```bash
git add database/seeders/RolePermissionSeeder.php
git commit -m "feat(config): assign config permissions to admin role"
```

---

## Phase 2: Backend - Barangay Management

### Task 2.1: Create Barangay Service with Tests (TDD)

**Files:**
- Create: `app/Services/Admin/Config/BarangayService.php`
- Create: `tests/Unit/Services/Admin/Config/BarangayServiceTest.php`

**Step 1: Create test file**

```bash
mkdir -p tests/Unit/Services/Admin/Config
```

**Step 2: Write failing test for getAllBarangays**

```php
<?php

namespace Tests\Unit\Services\Admin\Config;

use App\Models\Barangay;
use App\Models\Status;
use App\Services\Admin\Config\BarangayService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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
```

**Step 3: Run test to verify it fails**

```bash
docker compose exec water_billing_app php artisan test tests/Unit/Services/Admin/Config/BarangayServiceTest.php
```

Expected: FAIL - "Class 'BarangayService' not found"

**Step 4: Create service directory**

```bash
mkdir -p app/Services/Admin/Config
```

**Step 5: Write minimal BarangayService implementation**

```php
<?php

namespace App\Services\Admin\Config;

use App\Models\Barangay;
use Illuminate\Pagination\LengthAwarePaginator;

class BarangayService
{
    public function getAllBarangays(array $filters): array
    {
        $query = Barangay::query()->with('status');

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('b_desc', 'like', "%{$search}%")
                  ->orWhere('b_code', 'like', "%{$search}%");
            });
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('stat_id', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 15;
        $paginated = $query->orderBy('b_desc')->paginate($perPage);

        return [
            'data' => $paginated->items(),
            'links' => [
                'first' => $paginated->url(1),
                'last' => $paginated->url($paginated->lastPage()),
                'prev' => $paginated->previousPageUrl(),
                'next' => $paginated->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'from' => $paginated->firstItem(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'to' => $paginated->lastItem(),
                'total' => $paginated->total(),
            ],
        ];
    }
}
```

**Step 6: Run test to verify it passes**

```bash
docker compose exec water_billing_app php artisan test tests/Unit/Services/Admin/Config/BarangayServiceTest.php
```

Expected: PASS

**Step 7: Commit**

```bash
git add app/Services/Admin/Config/BarangayService.php tests/Unit/Services/Admin/Config/BarangayServiceTest.php
git commit -m "feat(config): add BarangayService with getAllBarangays method"
```

---

### Task 2.2: Add Create/Update/Delete Methods to BarangayService (TDD)

**Files:**
- Modify: `app/Services/Admin/Config/BarangayService.php`
- Modify: `tests/Unit/Services/Admin/Config/BarangayServiceTest.php`

**Step 1: Write failing tests**

Add to test file:

```php
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

    expect(fn() => $this->service->deleteBarangay($barangay->b_id))
        ->toThrow(\DomainException::class, 'Cannot delete barangay');
});

test('deleteBarangay throws exception when consumer addresses exist', function () {
    $barangay = Barangay::factory()->create();
    \App\Models\ConsumerAddress::factory()->create(['b_id' => $barangay->b_id]);

    expect(fn() => $this->service->deleteBarangay($barangay->b_id))
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
```

**Step 2: Run tests to verify they fail**

```bash
docker compose exec water_billing_app php artisan test tests/Unit/Services/Admin/Config/BarangayServiceTest.php
```

Expected: Multiple FAIL - methods not found

**Step 3: Implement methods in BarangayService**

```php
public function createBarangay(array $data): Barangay
{
    $data['stat_id'] = Status::getIdByDescription(Status::ACTIVE);

    return Barangay::create($data);
}

public function updateBarangay(int $id, array $data): Barangay
{
    $barangay = Barangay::findOrFail($id);
    $barangay->update($data);

    return $barangay->fresh();
}

public function deleteBarangay(int $id): void
{
    $barangay = Barangay::findOrFail($id);

    // Check for dependencies
    $puroksCount = $barangay->puroks()->count();
    if ($puroksCount > 0) {
        throw new \DomainException(
            "Cannot delete barangay '{$barangay->b_desc}' because it has {$puroksCount} associated puroks."
        );
    }

    $addressesCount = $barangay->consumerAddresses()->count();
    if ($addressesCount > 0) {
        throw new \DomainException(
            "Cannot delete barangay '{$barangay->b_desc}' because it is used in {$addressesCount} consumer addresses."
        );
    }

    $barangay->delete();
}

public function getBarangayDetails(int $id): Barangay
{
    return Barangay::with('status', 'puroks')
        ->withCount(['puroks', 'consumerAddresses as addresses_count'])
        ->findOrFail($id);
}
```

**Step 4: Run tests to verify they pass**

```bash
docker compose exec water_billing_app php artisan test tests/Unit/Services/Admin/Config/BarangayServiceTest.php
```

Expected: All PASS

**Step 5: Commit**

```bash
git add app/Services/Admin/Config/BarangayService.php tests/Unit/Services/Admin/Config/BarangayServiceTest.php
git commit -m "feat(config): add CRUD methods to BarangayService"
```

---

### Task 2.3: Create Barangay Form Requests

**Files:**
- Create: `app/Http/Requests/Admin/Config/StoreBarangayRequest.php`
- Create: `app/Http/Requests/Admin/Config/UpdateBarangayRequest.php`

**Step 1: Create directory**

```bash
mkdir -p app/Http/Requests/Admin/Config
```

**Step 2: Create StoreBarangayRequest**

```php
<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBarangayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('config.geographic.manage');
    }

    public function rules(): array
    {
        return [
            'b_desc' => [
                'required',
                'string',
                'max:100',
                Rule::unique('barangay', 'b_desc'),
            ],
            'b_code' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('barangay', 'b_code'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'b_desc.required' => 'Barangay name is required',
            'b_desc.unique' => 'A barangay with this name already exists',
            'b_code.unique' => 'This barangay code is already in use',
        ];
    }
}
```

**Step 3: Create UpdateBarangayRequest**

```php
<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBarangayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('config.geographic.manage');
    }

    public function rules(): array
    {
        $barangayId = $this->route('id');

        return [
            'b_desc' => [
                'required',
                'string',
                'max:100',
                Rule::unique('barangay', 'b_desc')->ignore($barangayId, 'b_id'),
            ],
            'b_code' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('barangay', 'b_code')->ignore($barangayId, 'b_id'),
            ],
            'stat_id' => [
                'required',
                'exists:statuses,stat_id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'b_desc.required' => 'Barangay name is required',
            'b_desc.unique' => 'A barangay with this name already exists',
            'b_code.unique' => 'This barangay code is already in use',
            'stat_id.required' => 'Status is required',
            'stat_id.exists' => 'Invalid status selected',
        ];
    }
}
```

**Step 4: Commit**

```bash
git add app/Http/Requests/Admin/Config/
git commit -m "feat(config): add barangay form request validation"
```

---

### Task 2.4: Create Barangay Controller with Tests (TDD)

**Files:**
- Create: `app/Http/Controllers/Admin/Config/BarangayController.php`
- Create: `tests/Feature/Admin/Config/BarangayManagementTest.php`

**Step 1: Create test directory and file**

```bash
mkdir -p tests/Feature/Admin/Config
```

**Step 2: Write failing feature tests**

```php
<?php

namespace Tests\Feature\Admin\Config;

use App\Models\Barangay;
use App\Models\ConsumerAddress;
use App\Models\Permission;
use App\Models\Purok;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create and assign permission
    $permission = Permission::factory()->create([
        'permission_name' => 'config.geographic.manage'
    ]);

    $role = Role::factory()->create();
    $role->permissions()->attach($permission->permission_id);
    $this->user->assignRole($role);

    $this->actingAs($this->user);
});

test('admin can view barangay list page', function () {
    $response = $this->get(route('config.barangays.index'));

    $response->assertOk();
    $response->assertViewIs('pages.admin.config.barangays.index');
});

test('admin can create new barangay', function () {
    $data = [
        'b_desc' => 'New Barangay',
        'b_code' => 'NB-001',
    ];

    $response = $this->postJson(route('config.barangays.store'), $data);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'message' => 'Barangay created successfully',
    ]);

    $this->assertDatabaseHas('barangay', [
        'b_desc' => 'New Barangay',
        'b_code' => 'NB-001',
    ]);
});

test('barangay name must be unique', function () {
    Barangay::factory()->create(['b_desc' => 'Poblacion']);

    $response = $this->postJson(route('config.barangays.store'), [
        'b_desc' => 'Poblacion',
        'b_code' => 'POB-001',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['b_desc']);
});

test('admin can update barangay', function () {
    $barangay = Barangay::factory()->create(['b_desc' => 'Old Name']);

    $response = $this->putJson(route('config.barangays.update', $barangay->b_id), [
        'b_desc' => 'Updated Name',
        'b_code' => 'UPD-001',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('barangay', [
        'b_id' => $barangay->b_id,
        'b_desc' => 'Updated Name',
    ]);
});

test('admin can view barangay details', function () {
    $barangay = Barangay::factory()->create();

    $response = $this->getJson(route('config.barangays.show', $barangay->b_id));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'b_id',
            'b_desc',
            'b_code',
            'puroks_count',
            'addresses_count',
        ]
    ]);
});

test('cannot delete barangay with existing puroks', function () {
    $barangay = Barangay::factory()->create();
    $barangay->puroks()->create([
        'p_desc' => 'Purok 1',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $response = $this->deleteJson(route('config.barangays.destroy', $barangay->b_id));

    $response->assertStatus(422);
    $response->assertJson(['success' => false]);
    $this->assertDatabaseHas('barangay', ['b_id' => $barangay->b_id]);
});

test('can delete barangay without dependencies', function () {
    $barangay = Barangay::factory()->create();

    $response = $this->deleteJson(route('config.barangays.destroy', $barangay->b_id));

    $response->assertOk();
    $response->assertJson(['success' => true]);
    $this->assertDatabaseMissing('barangay', ['b_id' => $barangay->b_id]);
});

test('user without permission cannot access barangay management', function () {
    $unauthorizedUser = User::factory()->create();
    $this->actingAs($unauthorizedUser);

    $response = $this->get(route('config.barangays.index'));

    $response->assertForbidden();
});
```

**Step 3: Run tests to verify they fail**

```bash
docker compose exec water_billing_app php artisan test tests/Feature/Admin/Config/BarangayManagementTest.php
```

Expected: FAIL - routes not found

**Step 4: Create controller directory**

```bash
mkdir -p app/Http/Controllers/Admin/Config
```

**Step 5: Create BarangayController**

```php
<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Config\StoreBarangayRequest;
use App\Http\Requests\Admin\Config\UpdateBarangayRequest;
use App\Services\Admin\Config\BarangayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class BarangayController extends Controller
{
    public function __construct(
        private BarangayService $barangayService
    ) {}

    public function index(): View
    {
        return view('pages.admin.config.barangays.index');
    }

    public function store(StoreBarangayRequest $request): JsonResponse
    {
        try {
            $barangay = $this->barangayService->createBarangay($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Barangay created successfully',
                'data' => $barangay,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create barangay', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create barangay',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $barangay = $this->barangayService->getBarangayDetails($id);

            return response()->json([
                'data' => $barangay,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Barangay not found',
            ], 404);
        }
    }

    public function update(UpdateBarangayRequest $request, int $id): JsonResponse
    {
        try {
            $barangay = $this->barangayService->updateBarangay($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Barangay updated successfully',
                'data' => $barangay,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update barangay', [
                'barangay_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update barangay',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->barangayService->deleteBarangay($id);

            return response()->json([
                'success' => true,
                'message' => 'Barangay deleted successfully',
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to delete barangay', [
                'barangay_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }
}
```

**Step 6: Add routes to web.php**

In `routes/web.php`, add within the auth middleware group:

```php
// Geographic Configuration Routes
Route::middleware(['permission:config.geographic.manage'])->prefix('config')->group(function () {
    Route::get('/barangays', [BarangayController::class, 'index'])->name('config.barangays.index');
    Route::post('/barangays', [BarangayController::class, 'store'])->name('config.barangays.store');
    Route::get('/barangays/{id}', [BarangayController::class, 'show'])->name('config.barangays.show');
    Route::put('/barangays/{id}', [BarangayController::class, 'update'])->name('config.barangays.update');
    Route::delete('/barangays/{id}', [BarangayController::class, 'destroy'])->name('config.barangays.destroy');
});
```

Add use statement at top:
```php
use App\Http\Controllers\Admin\Config\BarangayController;
```

**Step 7: Run tests to verify they pass (will still fail on view)**

```bash
docker compose exec water_billing_app php artisan test tests/Feature/Admin/Config/BarangayManagementTest.php
```

Expected: Some PASS, view test will fail (view not created yet)

**Step 8: Commit**

```bash
git add app/Http/Controllers/Admin/Config/BarangayController.php tests/Feature/Admin/Config/BarangayManagementTest.php routes/web.php
git commit -m "feat(config): add BarangayController with CRUD endpoints"
```

---

## Phase 3: Backend - Area Management

### Task 3.1: Create Area Service with Tests (TDD)

**Files:**
- Create: `app/Services/Admin/Config/AreaService.php`
- Create: `tests/Unit/Services/Admin/Config/AreaServiceTest.php`

**Step 1: Write failing tests**

```php
<?php

namespace Tests\Unit\Services\Admin\Config;

use App\Models\Area;
use App\Models\AreaAssignment;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\User;
use App\Services\Admin\Config\AreaService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new AreaService();
});

test('getAllAreas returns paginated results', function () {
    Area::factory()->count(5)->create();

    $result = $this->service->getAllAreas([]);

    expect($result)->toHaveKey('data')
        ->and($result['data'])->toHaveCount(5);
});

test('createArea sets active status by default', function () {
    $area = $this->service->createArea([
        'a_desc' => 'Test Area',
    ]);

    expect($area->stat_id)->toBe(Status::getIdByDescription(Status::ACTIVE))
        ->and($area->a_desc)->toBe('Test Area');
});

test('deleteArea throws exception when active assignments exist', function () {
    $area = Area::factory()->create();
    $user = User::factory()->create();

    AreaAssignment::create([
        'user_id' => $user->id,
        'area_id' => $area->a_id,
        'effective_date' => now(),
        'end_date' => null, // Active
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    expect(fn() => $this->service->deleteArea($area->a_id))
        ->toThrow(\DomainException::class, 'Cannot delete area');
});

test('deleteArea throws exception when service connections exist', function () {
    $area = Area::factory()->create();
    ServiceConnection::factory()->create(['area_id' => $area->a_id]);

    expect(fn() => $this->service->deleteArea($area->a_id))
        ->toThrow(\DomainException::class, 'Cannot delete area');
});

test('deleteArea succeeds when no dependencies', function () {
    $area = Area::factory()->create();

    $this->service->deleteArea($area->a_id);

    expect(Area::find($area->a_id))->toBeNull();
});

test('getAreaDetails returns area with relationships', function () {
    $area = Area::factory()->create();
    $user = User::factory()->create();

    AreaAssignment::create([
        'user_id' => $user->id,
        'area_id' => $area->a_id,
        'effective_date' => now(),
        'end_date' => null,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $details = $this->service->getAreaDetails($area->a_id);

    expect($details)->toHaveKey('active_assignments')
        ->and($details)->toHaveKey('service_connections_count');
});
```

**Step 2: Run tests to verify they fail**

```bash
docker compose exec water_billing_app php artisan test tests/Unit/Services/Admin/Config/AreaServiceTest.php
```

Expected: FAIL - AreaService not found

**Step 3: Implement AreaService**

```php
<?php

namespace App\Services\Admin\Config;

use App\Models\Area;
use App\Models\Status;

class AreaService
{
    public function getAllAreas(array $filters): array
    {
        $query = Area::query()->with('status');

        // Search filter
        if (!empty($filters['search'])) {
            $query->where('a_desc', 'like', "%{$filters['search']}%");
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('stat_id', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 15;
        $paginated = $query->withCount(['activeAreaAssignments', 'serviceConnections', 'consumers'])
            ->orderBy('a_desc')
            ->paginate($perPage);

        return [
            'data' => $paginated->items(),
            'links' => [
                'first' => $paginated->url(1),
                'last' => $paginated->url($paginated->lastPage()),
                'prev' => $paginated->previousPageUrl(),
                'next' => $paginated->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'from' => $paginated->firstItem(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'to' => $paginated->lastItem(),
                'total' => $paginated->total(),
            ],
        ];
    }

    public function createArea(array $data): Area
    {
        $data['stat_id'] = Status::getIdByDescription(Status::ACTIVE);

        return Area::create($data);
    }

    public function updateArea(int $id, array $data): Area
    {
        $area = Area::findOrFail($id);
        $area->update($data);

        return $area->fresh();
    }

    public function deleteArea(int $id): void
    {
        $area = Area::findOrFail($id);

        // Check for active assignments
        $activeAssignments = $area->areaAssignments()->whereNull('end_date')->count();
        if ($activeAssignments > 0) {
            throw new \DomainException(
                "Cannot delete area '{$area->a_desc}' because it has {$activeAssignments} active meter reader assignments."
            );
        }

        // Check for service connections
        $connectionCount = $area->serviceConnections()->count();
        if ($connectionCount > 0) {
            throw new \DomainException(
                "Cannot delete area '{$area->a_desc}' because it has {$connectionCount} service connections."
            );
        }

        $area->delete();
    }

    public function getAreaDetails(int $id): Area
    {
        $area = Area::with([
            'status',
            'areaAssignments' => function ($query) {
                $query->whereNull('end_date')
                    ->with('user:id,name,email');
            }
        ])
        ->withCount(['serviceConnections', 'consumers'])
        ->findOrFail($id);

        // Format active assignments
        $area->active_assignments = $area->areaAssignments->map(function ($assignment) {
            return [
                'assignment_id' => $assignment->aa_id,
                'user' => $assignment->user,
                'effective_date' => $assignment->effective_date,
                'end_date' => $assignment->end_date,
            ];
        });

        unset($area->areaAssignments);

        return $area;
    }
}
```

**Step 4: Add activeAreaAssignments scope to Area model**

In `app/Models/Area.php`, add:

```php
public function activeAreaAssignments()
{
    return $this->areaAssignments()->whereNull('end_date');
}
```

**Step 5: Run tests to verify they pass**

```bash
docker compose exec water_billing_app php artisan test tests/Unit/Services/Admin/Config/AreaServiceTest.php
```

Expected: All PASS

**Step 6: Commit**

```bash
git add app/Services/Admin/Config/AreaService.php tests/Unit/Services/Admin/Config/AreaServiceTest.php app/Models/Area.php
git commit -m "feat(config): add AreaService with CRUD methods"
```

---

### Task 3.2: Create Area Form Requests

**Files:**
- Create: `app/Http/Requests/Admin/Config/StoreAreaRequest.php`
- Create: `app/Http/Requests/Admin/Config/UpdateAreaRequest.php`

**Step 1: Create StoreAreaRequest**

```php
<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('config.geographic.manage');
    }

    public function rules(): array
    {
        return [
            'a_desc' => [
                'required',
                'string',
                'max:100',
                Rule::unique('area', 'a_desc'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'a_desc.required' => 'Area name is required',
            'a_desc.unique' => 'An area with this name already exists',
        ];
    }
}
```

**Step 2: Create UpdateAreaRequest**

```php
<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('config.geographic.manage');
    }

    public function rules(): array
    {
        $areaId = $this->route('id');

        return [
            'a_desc' => [
                'required',
                'string',
                'max:100',
                Rule::unique('area', 'a_desc')->ignore($areaId, 'a_id'),
            ],
            'stat_id' => [
                'required',
                'exists:statuses,stat_id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'a_desc.required' => 'Area name is required',
            'a_desc.unique' => 'An area with this name already exists',
            'stat_id.required' => 'Status is required',
            'stat_id.exists' => 'Invalid status selected',
        ];
    }
}
```

**Step 3: Commit**

```bash
git add app/Http/Requests/Admin/Config/StoreAreaRequest.php app/Http/Requests/Admin/Config/UpdateAreaRequest.php
git commit -m "feat(config): add area form request validation"
```

---

### Task 3.3: Create Area Controller with Tests

**Files:**
- Create: `app/Http/Controllers/Admin/Config/AreaConfigController.php`
- Create: `tests/Feature/Admin/Config/AreaManagementTest.php`

**Step 1: Write failing feature tests**

```php
<?php

namespace Tests\Feature\Admin\Config;

use App\Models\Area;
use App\Models\AreaAssignment;
use App\Models\Permission;
use App\Models\Role;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    $permission = Permission::factory()->create([
        'permission_name' => 'config.geographic.manage'
    ]);

    $role = Role::factory()->create();
    $role->permissions()->attach($permission->permission_id);
    $this->user->assignRole($role);

    $this->actingAs($this->user);
});

test('admin can view area list page', function () {
    $response = $this->get(route('config.areas.index'));

    $response->assertOk();
    $response->assertViewIs('pages.admin.config.areas.index');
});

test('admin can create new area', function () {
    $response = $this->postJson(route('config.areas.store'), [
        'a_desc' => 'New Area',
    ]);

    $response->assertStatus(201);
    $response->assertJson(['success' => true]);
    $this->assertDatabaseHas('area', ['a_desc' => 'New Area']);
});

test('cannot delete area with active assignments', function () {
    $area = Area::factory()->create();
    $meterReader = User::factory()->create();

    AreaAssignment::create([
        'user_id' => $meterReader->id,
        'area_id' => $area->a_id,
        'effective_date' => now(),
        'end_date' => null,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $response = $this->deleteJson(route('config.areas.destroy', $area->a_id));

    $response->assertStatus(422);
    $response->assertJson(['success' => false]);
});

test('can view area details with relationships', function () {
    $area = Area::factory()->create();
    $user = User::factory()->create();

    AreaAssignment::create([
        'user_id' => $user->id,
        'area_id' => $area->a_id,
        'effective_date' => now(),
        'end_date' => null,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $response = $this->getJson(route('config.areas.show', $area->a_id));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'a_id',
            'a_desc',
            'active_assignments',
            'service_connections_count',
        ]
    ]);
});
```

**Step 2: Run tests to verify they fail**

```bash
docker compose exec water_billing_app php artisan test tests/Feature/Admin/Config/AreaManagementTest.php
```

Expected: FAIL - routes not found

**Step 3: Create AreaConfigController**

```php
<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Config\StoreAreaRequest;
use App\Http\Requests\Admin\Config\UpdateAreaRequest;
use App\Services\Admin\Config\AreaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AreaConfigController extends Controller
{
    public function __construct(
        private AreaService $areaService
    ) {}

    public function index(): View
    {
        return view('pages.admin.config.areas.index');
    }

    public function store(StoreAreaRequest $request): JsonResponse
    {
        try {
            $area = $this->areaService->createArea($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Area created successfully',
                'data' => $area,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create area', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create area',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $area = $this->areaService->getAreaDetails($id);

            return response()->json([
                'data' => $area,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Area not found',
            ], 404);
        }
    }

    public function update(UpdateAreaRequest $request, int $id): JsonResponse
    {
        try {
            $area = $this->areaService->updateArea($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Area updated successfully',
                'data' => $area,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update area', [
                'area_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update area',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->areaService->deleteArea($id);

            return response()->json([
                'success' => true,
                'message' => 'Area deleted successfully',
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to delete area', [
                'area_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }
}
```

**Step 4: Add routes to web.php**

In the same config group as barangays:

```php
// Area Management
Route::get('/areas', [AreaConfigController::class, 'index'])->name('config.areas.index');
Route::post('/areas', [AreaConfigController::class, 'store'])->name('config.areas.store');
Route::get('/areas/{id}', [AreaConfigController::class, 'show'])->name('config.areas.show');
Route::put('/areas/{id}', [AreaConfigController::class, 'update'])->name('config.areas.update');
Route::delete('/areas/{id}', [AreaConfigController::class, 'destroy'])->name('config.areas.destroy');
```

Add use statement:
```php
use App\Http\Controllers\Admin\Config\AreaConfigController;
```

**Step 5: Run tests**

```bash
docker compose exec water_billing_app php artisan test tests/Feature/Admin/Config/AreaManagementTest.php
```

Expected: Most PASS (view test will fail)

**Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/Config/AreaConfigController.php tests/Feature/Admin/Config/AreaManagementTest.php routes/web.php
git commit -m "feat(config): add AreaConfigController with CRUD endpoints"
```

---

## Phase 4: Backend - Water Rate Management

### Task 4.1: Create Water Rate Service with Tests (TDD)

**Files:**
- Create: `app/Services/Admin/Config/WaterRateService.php`
- Create: `tests/Unit/Services/Admin/Config/WaterRateServiceTest.php`

**Step 1: Write failing tests**

```php
<?php

namespace Tests\Unit\Services\Admin\Config;

use App\Models\AccountType;
use App\Models\Status;
use App\Models\WaterRate;
use App\Services\Admin\Config\WaterRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new WaterRateService();
    $this->accountType = AccountType::factory()->create(['at_desc' => 'Residential']);
});

test('getAllRates returns rates grouped by account type', function () {
    WaterRate::factory()->count(4)->create([
        'class_id' => $this->accountType->at_id,
        'period_id' => null,
    ]);

    $result = $this->service->getAllRates();

    expect($result)->toHaveKey('data')
        ->and($result['data'])->toHaveKey('Residential')
        ->and($result['data']['Residential'])->toHaveCount(4);
});

test('createOrUpdateRateTier creates new tier', function () {
    $data = [
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 0,
        'range_max' => 10,
        'rate_val' => 100.00,
        'rate_inc' => 0.00,
    ];

    $tier = $this->service->createOrUpdateRateTier($data);

    expect($tier->range_min)->toBe(0.0)
        ->and($tier->range_max)->toBe(10.0)
        ->and($tier->rate_val)->toBe(100.00);
});

test('createOrUpdateRateTier updates existing tier', function () {
    $existing = WaterRate::create([
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 0,
        'range_max' => 10,
        'rate_val' => 100.00,
        'rate_inc' => 0.00,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $updated = $this->service->createOrUpdateRateTier([
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 0,
        'range_max' => 10,
        'rate_val' => 120.00, // Updated
        'rate_inc' => 0.00,
    ]);

    expect($updated->wr_id)->toBe($existing->wr_id)
        ->and($updated->rate_val)->toBe(120.00);
});

test('validateNoRangeOverlap throws exception for overlapping ranges', function () {
    // Create tier: 0-10
    WaterRate::create([
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 0,
        'range_max' => 10,
        'rate_val' => 100.00,
        'rate_inc' => 0.00,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Try to create overlapping tier: 5-15
    $newTier = [
        'range_min' => 5,
        'range_max' => 15,
    ];

    expect(fn() => $this->service->validateNoRangeOverlap(
        $this->accountType->at_id,
        null,
        $newTier
    ))->toThrow(\DomainException::class, 'overlaps');
});

test('validateNoRangeOverlap passes for non-overlapping ranges', function () {
    // Create tier: 0-10
    WaterRate::create([
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 0,
        'range_max' => 10,
        'rate_val' => 100.00,
        'rate_inc' => 0.00,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Create non-overlapping tier: 11-20
    $newTier = [
        'range_min' => 11,
        'range_max' => 20,
    ];

    $result = $this->service->validateNoRangeOverlap(
        $this->accountType->at_id,
        null,
        $newTier
    );

    expect($result)->toBeTrue();
});
```

**Step 2: Run tests to verify they fail**

```bash
docker compose exec water_billing_app php artisan test tests/Unit/Services/Admin/Config/WaterRateServiceTest.php
```

Expected: FAIL - WaterRateService not found

**Step 3: Implement WaterRateService**

```php
<?php

namespace App\Services\Admin\Config;

use App\Models\AccountType;
use App\Models\Status;
use App\Models\WaterRate;
use Illuminate\Support\Collection;

class WaterRateService
{
    public function getAllRates(?int $periodId = null): array
    {
        $rates = WaterRate::with('accountType')
            ->where('period_id', $periodId)
            ->orderBy('class_id')
            ->orderBy('range_id')
            ->get();

        // Group by account type
        $grouped = $rates->groupBy(function ($rate) {
            return $rate->accountType->at_desc;
        });

        return [
            'data' => $grouped->toArray(),
        ];
    }

    public function getRatesByAccountType(int $accountTypeId, ?int $periodId = null): Collection
    {
        return WaterRate::where('class_id', $accountTypeId)
            ->where('period_id', $periodId)
            ->orderBy('range_id')
            ->get();
    }

    public function createOrUpdateRateTier(array $data): WaterRate
    {
        // Validate no overlap before creating/updating
        $excludeId = null;

        // Check if updating existing tier
        $existing = WaterRate::where('period_id', $data['period_id'])
            ->where('class_id', $data['class_id'])
            ->where('range_id', $data['range_id'])
            ->first();

        if ($existing) {
            $excludeId = $existing->wr_id;
        }

        $this->validateNoRangeOverlap(
            $data['class_id'],
            $data['period_id'],
            $data,
            $excludeId
        );

        $data['stat_id'] = Status::getIdByDescription(Status::ACTIVE);

        return WaterRate::updateOrCreate(
            [
                'period_id' => $data['period_id'],
                'class_id' => $data['class_id'],
                'range_id' => $data['range_id'],
            ],
            $data
        );
    }

    public function deleteRateTier(int $id): void
    {
        $tier = WaterRate::findOrFail($id);
        $tier->delete();
    }

    public function validateNoRangeOverlap(
        int $classId,
        ?int $periodId,
        array $newTier,
        ?int $excludeId = null
    ): bool {
        $existingTiers = WaterRate::where('class_id', $classId)
            ->where('period_id', $periodId)
            ->when($excludeId, fn($q) => $q->where('wr_id', '!=', $excludeId))
            ->get();

        foreach ($existingTiers as $tier) {
            if ($this->rangesOverlap(
                $newTier['range_min'],
                $newTier['range_max'],
                $tier->range_min,
                $tier->range_max
            )) {
                throw new \DomainException(
                    "Range {$newTier['range_min']}-{$newTier['range_max']} overlaps with existing tier {$tier->range_id} ({$tier->range_min}-{$tier->range_max})"
                );
            }
        }

        return true;
    }

    private function rangesOverlap($min1, $max1, $min2, $max2): bool
    {
        return $min1 <= $max2 && $max1 >= $min2;
    }

    public function getAccountTypes(): Collection
    {
        return AccountType::withCount([
            'waterRates' => function ($query) {
                $query->whereNull('period_id');
            }
        ])->get();
    }
}
```

**Step 4: Add relationship to AccountType model**

In `app/Models/AccountType.php`, add:

```php
public function waterRates()
{
    return $this->hasMany(WaterRate::class, 'class_id', 'at_id');
}
```

**Step 5: Add relationship to WaterRate model**

Check if `app/Models/WaterRate.php` exists. If not, create it:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterRate extends Model
{
    protected $table = 'water_rates';

    protected $primaryKey = 'wr_id';

    public $timestamps = true;

    protected $fillable = [
        'period_id',
        'class_id',
        'range_id',
        'range_min',
        'range_max',
        'rate_val',
        'rate_inc',
        'stat_id',
    ];

    protected $casts = [
        'range_min' => 'float',
        'range_max' => 'float',
        'rate_val' => 'float',
        'rate_inc' => 'float',
    ];

    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'class_id', 'at_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id', 'per_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }
}
```

**Step 6: Run tests to verify they pass**

```bash
docker compose exec water_billing_app php artisan test tests/Unit/Services/Admin/Config/WaterRateServiceTest.php
```

Expected: All PASS

**Step 7: Commit**

```bash
git add app/Services/Admin/Config/WaterRateService.php tests/Unit/Services/Admin/Config/WaterRateServiceTest.php app/Models/WaterRate.php app/Models/AccountType.php
git commit -m "feat(config): add WaterRateService with overlap validation"
```

---

### Task 4.2: Create Water Rate Form Requests

**Files:**
- Create: `app/Http/Requests/Admin/Config/StoreWaterRateRequest.php`
- Create: `app/Http/Requests/Admin/Config/UpdateWaterRateRequest.php`

**Step 1: Create StoreWaterRateRequest**

```php
<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaterRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('config.billing.manage');
    }

    public function rules(): array
    {
        return [
            'period_id' => 'nullable|exists:period,per_id',
            'class_id' => 'required|exists:account_type,at_id',
            'range_id' => 'required|integer|min:1',
            'range_min' => 'required|numeric|min:0',
            'range_max' => 'required|numeric|gt:range_min',
            'rate_val' => 'required|numeric|min:0',
            'rate_inc' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'class_id.required' => 'Account type is required',
            'class_id.exists' => 'Invalid account type',
            'range_id.required' => 'Range tier is required',
            'range_min.required' => 'Minimum range is required',
            'range_max.required' => 'Maximum range is required',
            'range_max.gt' => 'Maximum range must be greater than minimum range',
            'rate_val.required' => 'Base rate is required',
            'rate_inc.required' => 'Rate increment is required',
        ];
    }
}
```

**Step 2: Create UpdateWaterRateRequest**

```php
<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWaterRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('config.billing.manage');
    }

    public function rules(): array
    {
        return [
            'period_id' => 'nullable|exists:period,per_id',
            'class_id' => 'required|exists:account_type,at_id',
            'range_id' => 'required|integer|min:1',
            'range_min' => 'required|numeric|min:0',
            'range_max' => 'required|numeric|gt:range_min',
            'rate_val' => 'required|numeric|min:0',
            'rate_inc' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'class_id.required' => 'Account type is required',
            'range_max.gt' => 'Maximum range must be greater than minimum range',
        ];
    }
}
```

**Step 3: Commit**

```bash
git add app/Http/Requests/Admin/Config/StoreWaterRateRequest.php app/Http/Requests/Admin/Config/UpdateWaterRateRequest.php
git commit -m "feat(config): add water rate form request validation"
```

---

### Task 4.3: Create Water Rate Controller with Tests

**Files:**
- Create: `app/Http/Controllers/Admin/Config/WaterRateController.php`
- Create: `tests/Feature/Admin/Config/WaterRateManagementTest.php`

**Step 1: Write failing feature tests**

```php
<?php

namespace Tests\Feature\Admin\Config;

use App\Models\AccountType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use App\Models\WaterRate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    $permission = Permission::factory()->create([
        'permission_name' => 'config.billing.manage'
    ]);

    $role = Role::factory()->create();
    $role->permissions()->attach($permission->permission_id);
    $this->user->assignRole($role);

    $this->actingAs($this->user);

    $this->accountType = AccountType::factory()->create(['at_desc' => 'Residential']);
});

test('admin can view water rates page', function () {
    $response = $this->get(route('config.water-rates.index'));

    $response->assertOk();
    $response->assertViewIs('pages.admin.config.water-rates.index');
});

test('admin can create water rate tier', function () {
    $data = [
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 0,
        'range_max' => 10,
        'rate_val' => 100.00,
        'rate_inc' => 0.00,
    ];

    $response = $this->postJson(route('config.water-rates.store'), $data);

    $response->assertStatus(201);
    $response->assertJson(['success' => true]);
    $this->assertDatabaseHas('water_rates', [
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
    ]);
});

test('range max must be greater than min', function () {
    $data = [
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 20,
        'range_max' => 10,
        'rate_val' => 100.00,
        'rate_inc' => 0.00,
    ];

    $response = $this->postJson(route('config.water-rates.store'), $data);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['range_max']);
});

test('cannot create overlapping rate ranges', function () {
    // Create first tier: 0-10
    WaterRate::create([
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 1,
        'range_min' => 0,
        'range_max' => 10,
        'rate_val' => 100.00,
        'rate_inc' => 0.00,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    // Try overlapping tier: 5-15
    $data = [
        'period_id' => null,
        'class_id' => $this->accountType->at_id,
        'range_id' => 2,
        'range_min' => 5,
        'range_max' => 15,
        'rate_val' => 150.00,
        'rate_inc' => 10.00,
    ];

    $response = $this->postJson(route('config.water-rates.store'), $data);

    $response->assertStatus(422);
    $response->assertJson(['success' => false]);
});

test('can retrieve rates by account type', function () {
    WaterRate::factory()->count(4)->create(['class_id' => $this->accountType->at_id]);

    $response = $this->getJson(route('config.water-rates.index'));

    $response->assertOk();
    $response->assertJsonStructure(['data']);
});

test('can get account types', function () {
    $response = $this->getJson(route('config.water-rates.account-types'));

    $response->assertOk();
    $response->assertJsonStructure(['data' => [['at_id', 'at_desc']]]);
});
```

**Step 2: Run tests to verify they fail**

```bash
docker compose exec water_billing_app php artisan test tests/Feature/Admin/Config/WaterRateManagementTest.php
```

Expected: FAIL - routes not found

**Step 3: Create WaterRateController**

```php
<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Config\StoreWaterRateRequest;
use App\Http\Requests\Admin\Config\UpdateWaterRateRequest;
use App\Services\Admin\Config\WaterRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class WaterRateController extends Controller
{
    public function __construct(
        private WaterRateService $waterRateService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            $periodId = $request->query('period_id');
            $rates = $this->waterRateService->getAllRates($periodId);

            return response()->json($rates);
        }

        return view('pages.admin.config.water-rates.index');
    }

    public function getAccountTypes(): JsonResponse
    {
        $accountTypes = $this->waterRateService->getAccountTypes();

        return response()->json([
            'data' => $accountTypes,
        ]);
    }

    public function store(StoreWaterRateRequest $request): JsonResponse
    {
        try {
            $tier = $this->waterRateService->createOrUpdateRateTier($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Rate tier created successfully',
                'data' => $tier,
            ], 201);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to create water rate tier', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create rate tier',
            ], 500);
        }
    }

    public function update(UpdateWaterRateRequest $request, int $id): JsonResponse
    {
        try {
            // For update, we pass the data with the tier ID
            $data = $request->validated();
            $tier = $this->waterRateService->createOrUpdateRateTier($data);

            return response()->json([
                'success' => true,
                'message' => 'Rate tier updated successfully',
                'data' => $tier,
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to update water rate tier', [
                'tier_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update rate tier',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->waterRateService->deleteRateTier($id);

            return response()->json([
                'success' => true,
                'message' => 'Rate tier deleted successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete water rate tier', [
                'tier_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete rate tier',
            ], 500);
        }
    }
}
```

**Step 4: Add routes to web.php**

```php
// Billing Configuration Routes
Route::middleware(['permission:config.billing.manage'])->prefix('config')->group(function () {
    Route::get('/water-rates', [WaterRateController::class, 'index'])->name('config.water-rates.index');
    Route::get('/water-rates/account-types', [WaterRateController::class, 'getAccountTypes'])->name('config.water-rates.account-types');
    Route::post('/water-rates', [WaterRateController::class, 'store'])->name('config.water-rates.store');
    Route::put('/water-rates/{id}', [WaterRateController::class, 'update'])->name('config.water-rates.update');
    Route::delete('/water-rates/{id}', [WaterRateController::class, 'destroy'])->name('config.water-rates.destroy');
});
```

Add use statement:
```php
use App\Http\Controllers\Admin\Config\WaterRateController;
```

**Step 5: Run tests**

```bash
docker compose exec water_billing_app php artisan test tests/Feature/Admin/Config/WaterRateManagementTest.php
```

Expected: Most PASS (view test will fail)

**Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/Config/WaterRateController.php tests/Feature/Admin/Config/WaterRateManagementTest.php routes/web.php
git commit -m "feat(config): add WaterRateController with CRUD endpoints"
```

---

## Phase 5: Backend - User Area Assignment

### Task 5.1: Modify UserController for Area Assignment (TDD)

**Files:**
- Modify: `app/Http/Controllers/User/UserController.php`
- Create: `tests/Feature/Admin/Config/UserAreaAssignmentTest.php`

**Step 1: Write failing tests**

```php
<?php

namespace Tests\Feature\Admin\Config;

use App\Models\Area;
use App\Models\AreaAssignment;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    $permission = Permission::factory()->create([
        'permission_name' => 'users.manage'
    ]);

    $role = Role::factory()->create();
    $role->permissions()->attach($permission->permission_id);
    $this->user->assignRole($role);

    $this->actingAs($this->user);
});

test('meter reader gets area assigned during creation', function () {
    $area = Area::factory()->create();
    $meterReaderRole = Role::factory()->create(['role_name' => 'meter_reader']);

    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'username' => 'johndoe',
        'password' => 'password123',
        'u_type' => 1,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'roles' => [$meterReaderRole->role_id],
        'meter_reader_areas' => [$area->a_id],
    ];

    $response = $this->postJson(route('user.store'), $userData);

    $response->assertStatus(201);

    $user = User::where('email', 'john@example.com')->first();

    $this->assertDatabaseHas('AreaAssignment', [
        'user_id' => $user->id,
        'area_id' => $area->a_id,
        'end_date' => null,
    ]);
});

test('meter reader can be assigned multiple areas', function () {
    $area1 = Area::factory()->create();
    $area2 = Area::factory()->create();
    $meterReaderRole = Role::factory()->create(['role_name' => 'meter_reader']);

    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'username' => 'johndoe',
        'password' => 'password123',
        'u_type' => 1,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'roles' => [$meterReaderRole->role_id],
        'meter_reader_areas' => [$area1->a_id, $area2->a_id],
    ];

    $response = $this->postJson(route('user.store'), $userData);

    $response->assertStatus(201);

    $user = User::where('email', 'john@example.com')->first();

    expect($user->areaAssignments)->toHaveCount(2);
});

test('non-meter-reader does not get area assignment', function () {
    $area = Area::factory()->create();
    $adminRole = Role::factory()->create(['role_name' => 'admin']);

    $userData = [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'username' => 'adminuser',
        'password' => 'password123',
        'u_type' => 1,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'roles' => [$adminRole->role_id],
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
    $meterReaderRole = Role::factory()->create(['role_name' => 'meter_reader']);

    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'username' => 'johndoe',
        'password' => 'password123',
        'u_type' => 1,
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'roles' => [$meterReaderRole->role_id],
        'meter_reader_areas' => [$area->a_id],
    ];

    $this->postJson(route('user.store'), $userData);

    $user = User::where('email', 'john@example.com')->first();
    $assignment = AreaAssignment::where('user_id', $user->id)->first();

    expect($assignment->effective_date)->toBe(now()->toDateString())
        ->and($assignment->end_date)->toBeNull()
        ->and($assignment->stat_id)->toBe(Status::getIdByDescription(Status::ACTIVE));
});
```

**Step 2: Run tests to verify they fail**

```bash
docker compose exec water_billing_app php artisan test tests/Feature/Admin/Config/UserAreaAssignmentTest.php
```

Expected: FAIL - area assignment not implemented

**Step 3: Create AreaAssignmentService if doesn't exist**

Check if `app/Services/AreaAssignmentService.php` exists. If not, create:

```bash
mkdir -p app/Services
```

```php
<?php

namespace App\Services;

use App\Models\AreaAssignment;
use App\Models\Status;

class AreaAssignmentService
{
    public function assignAreasToUser(int $userId, array $areaIds): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        foreach ($areaIds as $areaId) {
            AreaAssignment::create([
                'user_id' => $userId,
                'area_id' => $areaId,
                'effective_date' => now()->toDateString(),
                'end_date' => null,
                'stat_id' => $activeStatusId,
            ]);
        }
    }
}
```

**Step 4: Modify UserController store method**

In `app/Http/Controllers/User/UserController.php`, modify the `store()` method:

```php
use App\Services\AreaAssignmentService;
use Illuminate\Support\Facades\DB;

public function store(StoreUserRequest $request, UserService $userService, AreaAssignmentService $areaService)
{
    DB::beginTransaction();

    try {
        // Create user (existing logic)
        $user = $userService->createUser($request->validated());

        // Assign roles (existing logic)
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        // NEW: If meter reader role assigned and areas provided
        if ($user->hasRole(Role::METER_READER) && $request->has('meter_reader_areas')) {
            $areaService->assignAreasToUser($user->id, $request->meter_reader_areas);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user->load('roles', 'areaAssignments.area'),
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Failed to create user', [
            'error' => $e->getMessage(),
            'data' => $request->except('password'),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to create user',
        ], 500);
    }
}
```

**Step 5: Update StoreUserRequest validation**

In the user store request, add validation for meter_reader_areas:

```php
public function rules(): array
{
    return [
        // ... existing rules ...
        'meter_reader_areas' => 'nullable|array',
        'meter_reader_areas.*' => 'exists:area,a_id',
    ];
}
```

**Step 6: Run tests to verify they pass**

```bash
docker compose exec water_billing_app php artisan test tests/Feature/Admin/Config/UserAreaAssignmentTest.php
```

Expected: All PASS

**Step 7: Commit**

```bash
git add app/Services/AreaAssignmentService.php app/Http/Controllers/User/UserController.php tests/Feature/Admin/Config/UserAreaAssignmentTest.php
git commit -m "feat(config): add area assignment during meter reader creation"
```

---

## Phase 6: Frontend - Shared Components

### Task 6.1: Create Shared UI Components

**Files:**
- Create: `resources/views/components/ui/admin/config/shared/page-header.blade.php`
- Create: `resources/views/components/ui/admin/config/shared/search-filter.blade.php`
- Create: `resources/views/components/ui/admin/config/shared/status-badge.blade.php`
- Create: `resources/views/components/ui/admin/config/shared/action-buttons.blade.php`

**Step 1: Create directories**

```bash
mkdir -p resources/views/components/ui/admin/config/shared
```

**Step 2: Create page-header.blade.php**

```blade
@props([
    'title' => '',
    'subtitle' => '',
    'canCreate' => false,
    'createLabel' => 'Add New',
])

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $title }}</h1>
        @if($subtitle)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
        @endif
    </div>

    @if($canCreate)
    <button
        @click="$dispatch('create')"
        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center gap-2"
    >
        <i class="fas fa-plus text-sm"></i>
        <span>{{ $createLabel }}</span>
    </button>
    @endif
</div>
```

**Step 3: Create search-filter.blade.php**

```blade
@props([
    'statuses' => [],
    'placeholder' => 'Search...',
])

<div class="flex items-center gap-4 mb-4">
    <!-- Search Input -->
    <div class="flex-1">
        <div class="relative">
            <input
                type="text"
                x-model="search"
                @input.debounce.500ms="$dispatch('search')"
                placeholder="{{ $placeholder }}"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            />
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    @if(count($statuses) > 0)
    <!-- Status Filter -->
    <div class="w-48">
        <select
            x-model="statusFilter"
            @change="$dispatch('search')"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
        >
            @foreach($statuses as $status)
            <option value="{{ is_array($status) ? $status['value'] : $status }}">
                {{ is_array($status) ? $status['label'] : $status }}
            </option>
            @endforeach
        </select>
    </div>
    @endif
</div>
```

**Step 4: Create status-badge.blade.php**

```blade
@props([
    'status' => null,
])

@php
$statusId = is_object($status) ? $status->stat_id : $status;
$statusDesc = is_object($status) ? $status->stat_desc : ($statusId == 1 ? 'ACTIVE' : 'INACTIVE');

$classes = match($statusId) {
    1 => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    2 => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
};
@endphp

<span class="px-2 py-1 text-xs font-medium rounded-full {{ $classes }}">
    {{ $statusDesc }}
</span>
```

**Step 5: Create action-buttons.blade.php**

```blade
@props([
    'canEdit' => true,
    'canView' => true,
    'canDelete' => true,
])

<div class="flex items-center gap-2">
    @if($canView)
    <button
        @click="$dispatch('view')"
        class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
        title="View Details"
    >
        <i class="fas fa-eye text-sm"></i>
    </button>
    @endif

    @if($canEdit)
    <button
        @click="$dispatch('edit')"
        class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors"
        title="Edit"
    >
        <i class="fas fa-edit text-sm"></i>
    </button>
    @endif

    @if($canDelete)
    <button
        @click="$dispatch('delete')"
        class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
        title="Delete"
    >
        <i class="fas fa-trash text-sm"></i>
    </button>
    @endif
</div>
```

**Step 6: Commit**

```bash
git add resources/views/components/ui/admin/config/shared/
git commit -m "feat(config): add shared UI components for admin config"
```

---

### Task 6.2: Create Alpine.js Config Table Utility

**Files:**
- Create: `resources/js/components/admin/config/shared/configTable.js`

**Step 1: Create directories**

```bash
mkdir -p resources/js/components/admin/config/shared
```

**Step 2: Create configTable.js**

```javascript
/**
 * Reusable Alpine.js utility for config tables
 * Provides common functionality: search, filters, pagination, modals
 */
export default function configTable(fetchUrl) {
    return {
        // Data
        items: [],
        search: '',
        statusFilter: '',
        currentPage: 1,
        totalPages: 1,
        perPage: 15,
        loading: false,

        // Modal states
        showCreateModal: false,
        showEditModal: false,
        showViewModal: false,
        showDeleteModal: false,

        // Selected item
        selectedItem: null,

        // Form data
        form: {},
        errors: {},

        // Notifications
        showSuccess: false,
        showError: false,
        successMessage: '',
        errorMessage: '',

        // Initialize
        async init() {
            await this.fetchItems();
        },

        // Fetch items from API
        async fetchItems() {
            this.loading = true;

            try {
                const params = new URLSearchParams({
                    search: this.search,
                    status: this.statusFilter,
                    page: this.currentPage,
                    per_page: this.perPage,
                });

                const response = await fetch(`${fetchUrl}?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                this.items = data.data || [];
                this.totalPages = data.meta?.last_page || 1;

            } catch (error) {
                console.error('Failed to fetch items:', error);
                this.showErrorNotification('Failed to load data');
            } finally {
                this.loading = false;
            }
        },

        // Modal management
        openCreateModal() {
            this.form = {};
            this.errors = {};
            this.showCreateModal = true;
        },

        openEditModal(item) {
            this.selectedItem = item;
            this.form = { ...item };
            this.errors = {};
            this.showEditModal = true;
        },

        openViewModal(item) {
            this.selectedItem = item;
            this.showViewModal = true;
        },

        openDeleteModal(item) {
            this.selectedItem = item;
            this.showDeleteModal = true;
        },

        closeAllModals() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.showViewModal = false;
            this.showDeleteModal = false;
            this.selectedItem = null;
        },

        // Notifications
        showSuccessNotification(message) {
            this.successMessage = message;
            this.showSuccess = true;
            setTimeout(() => { this.showSuccess = false; }, 5000);
        },

        showErrorNotification(message) {
            this.errorMessage = message;
            this.showError = true;
            setTimeout(() => { this.showError = false; }, 5000);
        },

        // Pagination
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
                this.fetchItems();
            }
        },

        // Get CSRF token
        getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content;
        },
    };
}
```

**Step 3: Commit**

```bash
git add resources/js/components/admin/config/shared/configTable.js
git commit -m "feat(config): add Alpine.js config table utility"
```

---

## Phase 7: Frontend - Barangay Management UI

### Task 7.1: Create Barangay Main View

**Files:**
- Create: `resources/views/pages/admin/config/barangays/index.blade.php`

**Step 1: Create directory**

```bash
mkdir -p resources/views/pages/admin/config/barangays
```

**Step 2: Create index.blade.php**

```blade
<x-app-layout>
    <div x-data="barangayManager()" class="p-6">
        <!-- Page Header -->
        <x-ui.admin.config.shared.page-header
            title="Manage Barangays"
            subtitle="Create and manage barangays in Initao"
            :can-create="true"
            create-label="Add Barangay"
            @create="openCreateModal()"
        />

        <!-- Search & Filters -->
        <x-ui.admin.config.shared.search-filter
            :statuses="[
                ['value' => '', 'label' => 'All Statuses'],
                ['value' => '1', 'label' => 'Active'],
                ['value' => '2', 'label' => 'Inactive']
            ]"
            placeholder="Search by name or code..."
        />

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
            <p class="mt-4 text-gray-500">Loading barangays...</p>
        </div>

        <!-- Table -->
        <div x-show="!loading" class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <x-ui.admin.config.barangay.table />
        </div>

        <!-- Pagination -->
        <div x-show="!loading && totalPages > 1" class="mt-4 flex justify-center">
            <nav class="flex items-center gap-2">
                <button
                    @click="goToPage(currentPage - 1)"
                    :disabled="currentPage === 1"
                    class="px-3 py-2 rounded-lg border disabled:opacity-50"
                >
                    Previous
                </button>

                <template x-for="page in Array.from({length: totalPages}, (_, i) => i + 1)" :key="page">
                    <button
                        @click="goToPage(page)"
                        :class="page === currentPage ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'"
                        class="px-3 py-2 rounded-lg border"
                        x-text="page"
                    ></button>
                </template>

                <button
                    @click="goToPage(currentPage + 1)"
                    :disabled="currentPage === totalPages"
                    class="px-3 py-2 rounded-lg border disabled:opacity-50"
                >
                    Next
                </button>
            </nav>
        </div>

        <!-- Modals -->
        <x-ui.admin.config.barangay.modals.create-barangay />
        <x-ui.admin.config.barangay.modals.edit-barangay />
        <x-ui.admin.config.barangay.modals.view-barangay />
        <x-ui.admin.config.barangay.modals.delete-barangay />

        <!-- Success Notification -->
        <div x-show="showSuccess"
             x-transition
             class="fixed top-4 right-4 bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-800" x-text="successMessage"></p>
            </div>
        </div>

        <!-- Error Notification -->
        <div x-show="showError"
             x-transition
             class="fixed top-4 right-4 bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-red-800" x-text="errorMessage"></p>
            </div>
        </div>
    </div>
</x-app-layout>
```

**Step 3: Commit**

```bash
git add resources/views/pages/admin/config/barangays/index.blade.php
git commit -m "feat(config): add barangay management main view"
```

---

### Task 7.2: Create Barangay Table Component

**Files:**
- Create: `resources/views/components/ui/admin/config/barangay/table.blade.php`

**Step 1: Create directory**

```bash
mkdir -p resources/views/components/ui/admin/config/barangay
```

**Step 2: Create table.blade.php**

```blade
<table class="w-full">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Name
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Code
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Puroks
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Addresses
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        <template x-if="items.length === 0">
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-4"></i>
                    <p>No barangays found</p>
                </td>
            </tr>
        </template>

        <template x-for="barangay in items" :key="barangay.b_id">
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="barangay.b_desc"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-500 dark:text-gray-400" x-text="barangay.b_code || '-'"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <x-ui.admin.config.shared.status-badge :status="barangay.status" x-bind:status="barangay.stat_id" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-900 dark:text-white" x-text="barangay.puroks_count || 0"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-900 dark:text-white" x-text="barangay.addresses_count || 0"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <x-ui.admin.config.shared.action-buttons
                        @view="openViewModal(barangay)"
                        @edit="openEditModal(barangay)"
                        @delete="openDeleteModal(barangay)"
                    />
                </td>
            </tr>
        </template>
    </tbody>
</table>
```

**Step 3: Commit**

```bash
git add resources/views/components/ui/admin/config/barangay/table.blade.php
git commit -m "feat(config): add barangay table component"
```

---

## Phase 8: Frontend - Area Management UI

### Task 8.1: Create Area Main View

**Files:**
- Create: `resources/views/pages/admin/config/areas/index.blade.php`

**Step 1: Create directory**

```bash
mkdir -p resources/views/pages/admin/config/areas
```

**Step 2: Create index.blade.php**

```blade
<x-app-layout>
    <div x-data="areaManager()" class="p-6">
        <!-- Page Header -->
        <x-ui.admin.config.shared.page-header
            title="Manage Areas"
            subtitle="Create and manage service areas in Initao"
            :can-create="true"
            create-label="Add Area"
            @create="openCreateModal()"
        />

        <!-- Search & Filters -->
        <x-ui.admin.config.shared.search-filter
            :statuses="[
                ['value' => '', 'label' => 'All Statuses'],
                ['value' => '1', 'label' => 'Active'],
                ['value' => '2', 'label' => 'Inactive']
            ]"
            placeholder="Search by area name..."
        />

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
            <p class="mt-4 text-gray-500">Loading areas...</p>
        </div>

        <!-- Table -->
        <div x-show="!loading" class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <x-ui.admin.config.area.table />
        </div>

        <!-- Pagination -->
        <div x-show="!loading && totalPages > 1" class="mt-4 flex justify-center">
            <nav class="flex items-center gap-2">
                <button
                    @click="goToPage(currentPage - 1)"
                    :disabled="currentPage === 1"
                    class="px-3 py-2 rounded-lg border disabled:opacity-50"
                >
                    Previous
                </button>

                <template x-for="page in Array.from({length: totalPages}, (_, i) => i + 1)" :key="page">
                    <button
                        @click="goToPage(page)"
                        :class="page === currentPage ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'"
                        class="px-3 py-2 rounded-lg border"
                        x-text="page"
                    ></button>
                </template>

                <button
                    @click="goToPage(currentPage + 1)"
                    :disabled="currentPage === totalPages"
                    class="px-3 py-2 rounded-lg border disabled:opacity-50"
                >
                    Next
                </button>
            </nav>
        </div>

        <!-- Modals -->
        <x-ui.admin.config.area.modals.create-area />
        <x-ui.admin.config.area.modals.edit-area />
        <x-ui.admin.config.area.modals.view-area />
        <x-ui.admin.config.area.modals.delete-area />

        <!-- Success Notification -->
        <div x-show="showSuccess"
             x-transition
             class="fixed top-4 right-4 bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-800" x-text="successMessage"></p>
            </div>
        </div>

        <!-- Error Notification -->
        <div x-show="showError"
             x-transition
             class="fixed top-4 right-4 bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-red-800" x-text="errorMessage"></p>
            </div>
        </div>
    </div>
</x-app-layout>
```

**Step 3: Commit**

```bash
git add resources/views/pages/admin/config/areas/index.blade.php
git commit -m "feat(config): add area management main view"
```

---

### Task 8.2: Create Area Table Component

**Files:**
- Create: `resources/views/components/ui/admin/config/area/table.blade.php`

**Step 1: Create directory**

```bash
mkdir -p resources/views/components/ui/admin/config/area
```

**Step 2: Create table.blade.php**

```blade
<table class="w-full">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Area Name
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Assigned Meter Readers
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Service Connections
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        <template x-if="items.length === 0">
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-4"></i>
                    <p>No areas found</p>
                </td>
            </tr>
        </template>

        <template x-for="area in items" :key="area.a_id">
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="area.a_desc"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span x-bind:class="area.stat_id == 1 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'" class="px-2 py-1 text-xs font-medium rounded-full" x-text="area.stat_id == 1 ? 'ACTIVE' : 'INACTIVE'"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-900 dark:text-white" x-text="area.meter_readers_count || 0"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-900 dark:text-white" x-text="area.service_connections_count || 0"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button
                            @click="openViewModal(area)"
                            class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                            title="View Details"
                        >
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                        <button
                            @click="openEditModal(area)"
                            class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors"
                            title="Edit"
                        >
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                        <button
                            @click="openDeleteModal(area)"
                            class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                            title="Delete"
                        >
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </template>
    </tbody>
</table>
```

**Step 3: Commit**

```bash
git add resources/views/components/ui/admin/config/area/table.blade.php
git commit -m "feat(config): add area table component"
```

---

### Task 8.3: Create areaManager Alpine.js Function

**Files:**
- Create: `resources/js/components/admin/config/areas/areaManager.js`
- Modify: `resources/js/app.js`

**Step 1: Create directory**

```bash
mkdir -p resources/js/components/admin/config/areas
```

**Step 2: Create areaManager.js**

```javascript
import configTable from '../shared/configTable.js';

/**
 * Area Manager - extends configTable with area-specific operations
 */
export default function areaManager() {
    return {
        ...configTable('/config/areas'),

        // Create area
        async createArea() {
            this.errors = {};

            try {
                const response = await fetch('/config/areas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors || {};
                        throw new Error(data.message || 'Validation failed');
                    }
                    throw new Error(data.message || 'Failed to create area');
                }

                this.showSuccessNotification('Area created successfully');
                this.closeAllModals();
                await this.fetchItems();

            } catch (error) {
                console.error('Create area error:', error);
                this.showErrorNotification(error.message || 'Failed to create area');
            }
        },

        // Update area
        async updateArea() {
            this.errors = {};

            try {
                const response = await fetch(`/config/areas/${this.selectedItem.a_id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors || {};
                        throw new Error(data.message || 'Validation failed');
                    }
                    throw new Error(data.message || 'Failed to update area');
                }

                this.showSuccessNotification('Area updated successfully');
                this.closeAllModals();
                await this.fetchItems();

            } catch (error) {
                console.error('Update area error:', error);
                this.showErrorNotification(error.message || 'Failed to update area');
            }
        },

        // Delete area
        async deleteArea() {
            try {
                const response = await fetch(`/config/areas/${this.selectedItem.a_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete area');
                }

                this.showSuccessNotification('Area deleted successfully');
                this.closeAllModals();
                await this.fetchItems();

            } catch (error) {
                console.error('Delete area error:', error);
                this.showErrorNotification(error.message || 'Failed to delete area');
            }
        },
    };
}

// Make it available globally for Alpine.js
window.areaManager = areaManager;
```

**Step 3: Import in app.js**

Add after barangayManager import:

```javascript
import './components/admin/config/areas/areaManager.js';
```

**Step 4: Commit**

```bash
git add resources/js/components/admin/config/areas/areaManager.js resources/js/app.js
git commit -m "feat(config): add areaManager Alpine.js function"
```

---

### Task 8.4: Create Area Modal Components

**Files:**
- Create: `resources/views/components/ui/admin/config/area/modals/create-area.blade.php`
- Create: `resources/views/components/ui/admin/config/area/modals/edit-area.blade.php`
- Create: `resources/views/components/ui/admin/config/area/modals/view-area.blade.php`
- Create: `resources/views/components/ui/admin/config/area/modals/delete-area.blade.php`

**Step 1: Create directory**

```bash
mkdir -p resources/views/components/ui/admin/config/area/modals
```

**Step 2: Create create-area.blade.php**

```blade
<!-- Create Area Modal -->
<div x-show="showCreateModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAllModals()"></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Add New Area
                </h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Body -->
            <form @submit.prevent="createArea()" class="p-6 space-y-4">
                <!-- Area Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Area Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        x-model="form.a_desc"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        :class="{'border-red-500': errors.a_desc}"
                        placeholder="Enter area name"
                        required
                    />
                    <template x-if="errors.a_desc">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.a_desc[0]"></p>
                    </template>
                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-3 pt-4">
                    <button
                        type="button"
                        @click="closeAllModals()"
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    >
                        Create Area
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

**Step 3: Create edit-area.blade.php**

```blade
<!-- Edit Area Modal -->
<div x-show="showEditModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAllModals()"></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Edit Area
                </h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Body -->
            <form @submit.prevent="updateArea()" class="p-6 space-y-4">
                <!-- Area Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Area Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        x-model="form.a_desc"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        :class="{'border-red-500': errors.a_desc}"
                        placeholder="Enter area name"
                        required
                    />
                    <template x-if="errors.a_desc">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.a_desc[0]"></p>
                    </template>
                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-3 pt-4">
                    <button
                        type="button"
                        @click="closeAllModals()"
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    >
                        Update Area
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

**Step 4: Create view-area.blade.php**

```blade
<!-- View Area Modal -->
<div x-show="showViewModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAllModals()"></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Area Details
                </h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <!-- Area Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                        Area Name
                    </label>
                    <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.a_desc || '-'"></p>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                        Status
                    </label>
                    <div class="mt-1">
                        <span x-bind:class="selectedItem?.stat_id == 1 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'" class="px-2 py-1 text-xs font-medium rounded-full" x-text="selectedItem?.stat_id == 1 ? 'ACTIVE' : 'INACTIVE'"></span>
                    </div>
                </div>

                <!-- Meter Readers Count -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                        Assigned Meter Readers
                    </label>
                    <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.meter_readers_count || 0"></p>
                </div>

                <!-- Service Connections Count -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                        Service Connections
                    </label>
                    <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.service_connections_count || 0"></p>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 p-6 border-t dark:border-gray-700">
                <button
                    type="button"
                    @click="closeAllModals()"
                    class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
```

**Step 5: Create delete-area.blade.php**

```blade
<!-- Delete Area Modal -->
<div x-show="showDeleteModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAllModals()"></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/20 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-500"></i>
                    </div>
                    <h3 class="ml-4 text-xl font-semibold text-gray-900 dark:text-white">
                        Delete Area
                    </h3>
                </div>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">
                <p class="text-gray-700 dark:text-gray-300">
                    Are you sure you want to delete
                    <span class="font-semibold" x-text="selectedItem?.a_desc"></span>?
                </p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    This action cannot be undone. This area will be permanently removed from the system.
                </p>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 p-6 border-t dark:border-gray-700">
                <button
                    type="button"
                    @click="closeAllModals()"
                    class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    @click="deleteArea()"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                >
                    Delete Area
                </button>
            </div>
        </div>
    </div>
</div>
```

**Step 6: Commit**

```bash
git add resources/views/components/ui/admin/config/area/modals/
git commit -m "feat(config): add area modal components"
```

---

## Phase 9: Frontend - Water Rate Management UI

### Task 9.1: Create Water Rate Main View

**Files:**
- Create: `resources/views/pages/admin/config/water-rates/index.blade.php`

**Step 1: Create directory**

```bash
mkdir -p resources/views/pages/admin/config/water-rates
```

**Step 2: Create index.blade.php**

```blade
<x-app-layout>
    <div x-data="waterRateManager()" class="p-6">
        <!-- Page Header -->
        <x-ui.admin.config.shared.page-header
            title="Manage Water Rates"
            subtitle="Configure tiered water rate pricing by account type"
            :can-create="true"
            create-label="Add Rate Tier"
            @create="openCreateModal()"
        />

        <!-- Filters -->
        <div class="flex items-center gap-4 mb-4">
            <!-- Period Filter -->
            <div class="w-64">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Period
                </label>
                <select
                    x-model="periodFilter"
                    @change="fetchItems()"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                >
                    <option value="">Default Rates</option>
                    <!-- Periods will be loaded dynamically -->
                </select>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
            <p class="mt-4 text-gray-500">Loading water rates...</p>
        </div>

        <!-- Rates by Account Type -->
        <div x-show="!loading" class="space-y-6">
            <template x-for="(tiers, accountType) in items" :key="accountType">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="accountType"></h3>
                    </div>
                    <x-ui.admin.config.water-rate.table :account-type="accountType" />
                </div>
            </template>

            <template x-if="Object.keys(items).length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No water rates configured</p>
                </div>
            </template>
        </div>

        <!-- Modals -->
        <x-ui.admin.config.water-rate.modals.create-rate />
        <x-ui.admin.config.water-rate.modals.edit-rate />
        <x-ui.admin.config.water-rate.modals.view-rate />
        <x-ui.admin.config.water-rate.modals.delete-rate />

        <!-- Success Notification -->
        <div x-show="showSuccess"
             x-transition
             class="fixed top-4 right-4 bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-800" x-text="successMessage"></p>
            </div>
        </div>

        <!-- Error Notification -->
        <div x-show="showError"
             x-transition
             class="fixed top-4 right-4 bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-red-800" x-text="errorMessage"></p>
            </div>
        </div>
    </div>
</x-app-layout>
```

**Step 3: Commit**

```bash
git add resources/views/pages/admin/config/water-rates/index.blade.php
git commit -m "feat(config): add water rate management main view"
```

---

### Task 9.2: Create Water Rate Table Component

**Files:**
- Create: `resources/views/components/ui/admin/config/water-rate/table.blade.php`

**Step 1: Create directory**

```bash
mkdir -p resources/views/components/ui/admin/config/water-rate
```

**Step 2: Create table.blade.php**

```blade
@props(['accountType' => ''])

<table class="w-full">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Tier
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Range (m³)
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Base Rate
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Increment Rate
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        <template x-for="tier in items['{{ $accountType }}']" :key="tier.range_id">
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="'Tier ' + tier.range_id"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-900 dark:text-white" x-text="tier.range_min + ' - ' + tier.range_max"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-900 dark:text-white" x-text="'₱' + parseFloat(tier.rate_val).toFixed(2)"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-900 dark:text-white" x-text="'₱' + parseFloat(tier.rate_inc).toFixed(2)"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span x-bind:class="tier.stat_id == 1 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'" class="px-2 py-1 text-xs font-medium rounded-full" x-text="tier.stat_id == 1 ? 'ACTIVE' : 'INACTIVE'"></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button
                            @click="openViewModal(tier)"
                            class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                            title="View Details"
                        >
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                        <button
                            @click="openEditModal(tier)"
                            class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors"
                            title="Edit"
                        >
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                        <button
                            @click="openDeleteModal(tier)"
                            class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                            title="Delete"
                        >
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </template>
    </tbody>
</table>
```

**Step 3: Commit**

```bash
git add resources/views/components/ui/admin/config/water-rate/table.blade.php
git commit -m "feat(config): add water rate table component"
```

---

### Task 9.3: Create waterRateManager Alpine.js Function

**Files:**
- Create: `resources/js/components/admin/config/water-rates/waterRateManager.js`
- Modify: `resources/js/app.js`

**Step 1: Create directory**

```bash
mkdir -p resources/js/components/admin/config/water-rates
```

**Step 2: Create waterRateManager.js**

```javascript
import configTable from '../shared/configTable.js';

/**
 * Water Rate Manager - extends configTable with rate-specific operations
 */
export default function waterRateManager() {
    return {
        ...configTable('/config/water-rates'),

        // Period filter
        periodFilter: '',

        // Account types for dropdown
        accountTypes: [],

        // Override init to fetch account types
        async init() {
            await this.fetchAccountTypes();
            await this.fetchItems();
        },

        // Fetch account types
        async fetchAccountTypes() {
            try {
                const response = await fetch('/config/water-rates/account-types', {
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();
                this.accountTypes = data.data || [];

            } catch (error) {
                console.error('Failed to fetch account types:', error);
            }
        },

        // Override fetchItems to handle period filter
        async fetchItems() {
            this.loading = true;

            try {
                const params = new URLSearchParams({
                    period_id: this.periodFilter,
                });

                const response = await fetch(`/config/water-rates?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                // Data comes grouped by account type
                this.items = data.data || {};

            } catch (error) {
                console.error('Failed to fetch items:', error);
                this.showErrorNotification('Failed to load data');
            } finally {
                this.loading = false;
            }
        },

        // Create water rate
        async createWaterRate() {
            this.errors = {};

            try {
                const response = await fetch('/config/water-rates', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors || {};
                        throw new Error(data.message || 'Validation failed');
                    }
                    throw new Error(data.message || 'Failed to create water rate');
                }

                this.showSuccessNotification('Water rate tier created successfully');
                this.closeAllModals();
                await this.fetchItems();

            } catch (error) {
                console.error('Create water rate error:', error);
                this.showErrorNotification(error.message || 'Failed to create water rate');
            }
        },

        // Update water rate
        async updateWaterRate() {
            this.errors = {};

            try {
                const response = await fetch(`/config/water-rates/${this.selectedItem.range_id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors || {};
                        throw new Error(data.message || 'Validation failed');
                    }
                    throw new Error(data.message || 'Failed to update water rate');
                }

                this.showSuccessNotification('Water rate tier updated successfully');
                this.closeAllModals();
                await this.fetchItems();

            } catch (error) {
                console.error('Update water rate error:', error);
                this.showErrorNotification(error.message || 'Failed to update water rate');
            }
        },

        // Delete water rate
        async deleteWaterRate() {
            try {
                const response = await fetch(`/config/water-rates/${this.selectedItem.range_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete water rate');
                }

                this.showSuccessNotification('Water rate tier deleted successfully');
                this.closeAllModals();
                await this.fetchItems();

            } catch (error) {
                console.error('Delete water rate error:', error);
                this.showErrorNotification(error.message || 'Failed to delete water rate');
            }
        },
    };
}

// Make it available globally for Alpine.js
window.waterRateManager = waterRateManager;
```

**Step 3: Import in app.js**

Add after areaManager import:

```javascript
import './components/admin/config/water-rates/waterRateManager.js';
```

**Step 4: Commit**

```bash
git add resources/js/components/admin/config/water-rates/waterRateManager.js resources/js/app.js
git commit -m "feat(config): add waterRateManager Alpine.js function"
```

---

### Task 9.4: Create Water Rate Modal Components

**Files:**
- Create: `resources/views/components/ui/admin/config/water-rate/modals/create-rate.blade.php`
- Create: `resources/views/components/ui/admin/config/water-rate/modals/edit-rate.blade.php`
- Create: `resources/views/components/ui/admin/config/water-rate/modals/view-rate.blade.php`
- Create: `resources/views/components/ui/admin/config/water-rate/modals/delete-rate.blade.php`

**Step 1: Create directory**

```bash
mkdir -p resources/views/components/ui/admin/config/water-rate/modals
```

**Step 2: Create create-rate.blade.php**

```blade
<!-- Create Water Rate Modal -->
<div x-show="showCreateModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAllModals()"></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Add New Rate Tier
                </h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Body -->
            <form @submit.prevent="createWaterRate()" class="p-6 space-y-4">
                <!-- Account Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Account Type <span class="text-red-500">*</span>
                    </label>
                    <select
                        x-model="form.class_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        :class="{'border-red-500': errors.class_id}"
                        required
                    >
                        <option value="">Select account type</option>
                        <template x-for="type in accountTypes" :key="type.at_id">
                            <option :value="type.at_id" x-text="type.at_desc"></option>
                        </template>
                    </select>
                    <template x-if="errors.class_id">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.class_id[0]"></p>
                    </template>
                </div>

                <!-- Tier Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tier Number <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        x-model="form.range_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        :class="{'border-red-500': errors.range_id}"
                        placeholder="1, 2, 3..."
                        min="1"
                        required
                    />
                    <template x-if="errors.range_id">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.range_id[0]"></p>
                    </template>
                </div>

                <!-- Range Min -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Range Min (m³) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        x-model="form.range_min"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        :class="{'border-red-500': errors.range_min}"
                        placeholder="0"
                        min="0"
                        required
                    />
                    <template x-if="errors.range_min">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.range_min[0]"></p>
                    </template>
                </div>

                <!-- Range Max -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Range Max (m³) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        x-model="form.range_max"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        :class="{'border-red-500': errors.range_max}"
                        placeholder="10"
                        min="0"
                        required
                    />
                    <template x-if="errors.range_max">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.range_max[0]"></p>
                    </template>
                </div>

                <!-- Base Rate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Base Rate (₱) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        x-model="form.rate_val"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        :class="{'border-red-500': errors.rate_val}"
                        placeholder="0.00"
                        min="0"
                        required
                    />
                    <template x-if="errors.rate_val">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.rate_val[0]"></p>
                    </template>
                </div>

                <!-- Increment Rate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Increment Rate (₱) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        x-model="form.rate_inc"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        :class="{'border-red-500': errors.rate_inc}"
                        placeholder="0.00"
                        min="0"
                        required
                    />
                    <template x-if="errors.rate_inc">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.rate_inc[0]"></p>
                    </template>
                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-3 pt-4">
                    <button
                        type="button"
                        @click="closeAllModals()"
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    >
                        Create Rate Tier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

**Step 3: Create edit-rate.blade.php, view-rate.blade.php, delete-rate.blade.php**

(Similar structure to create modal, adjusted for edit/view/delete operations)

**Step 4: Commit**

```bash
git add resources/views/components/ui/admin/config/water-rate/modals/
git commit -m "feat(config): add water rate modal components"
```

---

## Phase 10: Navigation & Menu Restructuring

### Task 10.1: Create Admin Configuration Menu Section

**Files:**
- Modify: `resources/views/layouts/navigation.blade.php` (or equivalent)

**Step 1: Read current navigation structure**

```bash
# Find navigation file
find resources/views -name "*navigation*" -o -name "*sidebar*" -o -name "*menu*"
```

**Step 2: Add Admin Configuration section**

Add new menu section with permission check:

```blade
@can('config.geographic.manage', 'config.billing.manage')
<!-- Admin Configuration Menu -->
<div class="mb-6">
    <h3 class="px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
        Configuration
    </h3>

    <!-- Geographic Configuration -->
    @can('config.geographic.manage')
    <div x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-map-marked-alt w-5"></i>
                <span class="ml-3">Geographic</span>
            </div>
            <i class="fas fa-chevron-down transition-transform" :class="{'rotate-180': open}"></i>
        </button>

        <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
            <a href="{{ route('config.barangays.index') }}" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                Barangays
            </a>
            <a href="{{ route('config.areas.index') }}" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                Service Areas
            </a>
        </div>
    </div>
    @endcan

    <!-- Billing Configuration -->
    @can('config.billing.manage')
    <a href="{{ route('config.water-rates.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
        <i class="fas fa-dollar-sign w-5"></i>
        <span class="ml-3">Water Rates</span>
    </a>
    @endcan

    <!-- RBAC Configuration -->
    @can('users.manage')
    <div x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-shield-alt w-5"></i>
                <span class="ml-3">Access Control</span>
            </div>
            <i class="fas fa-chevron-down transition-transform" :class="{'rotate-180': open}"></i>
        </button>

        <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
            <a href="{{ route('roles.index') }}" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                Roles
            </a>
            <a href="{{ route('permissions.index') }}" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                Permissions
            </a>
            <a href="{{ route('permission-matrix.index') }}" class="block px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                Permission Matrix
            </a>
        </div>
    </div>
    @endcan
</div>
@endcan
```

**Step 3: Remove roles/permissions from User Management section**

Remove or comment out the old menu items under User Management.

**Step 4: Test navigation**

Verify permissions work correctly:
- Admin sees all config sections
- Users without permissions don't see menu

**Step 5: Commit**

```bash
git add resources/views/layouts/navigation.blade.php
git commit -m "feat(config): restructure navigation with admin configuration menu

- Created dedicated Configuration section
- Moved barangays and areas under Geographic submenu
- Added Water Rates menu item
- Moved roles/permissions under Access Control submenu
- Applied RBAC permissions to menu visibility

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

## Plan Execution Complete

**All phases documented:**
- ✅ Phase 1: Database & Permissions Setup
- ✅ Phase 2: Backend - Barangay Management
- ✅ Phase 3: Backend - Area Management
- ✅ Phase 4: Backend - Water Rate Management
- ✅ Phase 5: Backend - User Area Assignment
- ✅ Phase 6: Frontend - Shared Components
- ✅ Phase 7: Frontend - Barangay Management UI
- ✅ Phase 8: Frontend - Area Management UI
- ✅ Phase 9: Frontend - Water Rate Management UI
- ✅ Phase 10: Navigation & Menu Restructuring

**Ready for execution with superpowers:executing-plans**
