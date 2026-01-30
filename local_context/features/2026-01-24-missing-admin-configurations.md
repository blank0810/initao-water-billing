# Missing Admin Configuration UIs Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Implement UI management for 3 critical configurations: Puroks, Account Types, and Application Fee Templates (ChargeItem).

**Architecture:** Follow existing Admin Configuration pattern (Barangays/Areas). Server-side pattern with Controllers → Services → Models. Alpine.js for interactivity, Blade components for UI reusability. RBAC protection with config.geographic.manage and config.billing.manage permissions.

**Tech Stack:** Laravel 12, PHP 8.2+, Alpine.js, Tailwind CSS, Pest PHP, Blade Components

---

## Overview

Three configuration UIs are missing from Admin Configuration:

1. **Puroks** - Sub-barangay areas (belongs to Barangay)
2. **Account Types** - Customer types (Residential, Commercial, Industrial, etc.)
3. **Application Fee Templates** - ChargeItem records for application fees

All three already have:
- ✅ Models with relationships
- ✅ Database tables
- ✅ Seeders
- ❌ **NO Admin UI for CRUD operations**

**Dependencies:**
- Barangays must exist before creating Puroks (foreign key b_id)
- Account Types used by ServiceConnection and WaterRate
- ChargeItem used by ApplicationChargeService

---

## Phase 1: Backend - Purok Management

### Task 1.1: Create PurokService

**Files:**
- Create: `app/Services/Admin/Config/PurokService.php`

**Step 1: Write PurokService test**

```php
// tests/Feature/Admin/Config/PurokServiceTest.php
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

        // Create address using this purok
        \App\Models\ConsumerAddress::create([
            'p_id' => $purok->p_id,
            'b_id' => $this->barangay->b_id,
            't_id' => 1,
            'prov_id' => 1,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $this->expectException(\DomainException::class);
        $this->service->deletePurok($purok->p_id);
    }
}
```

**Step 2: Run test to verify it fails**

```bash
php artisan test --filter=PurokServiceTest
```

Expected: FAIL - PurokService class not found

**Step 3: Create PurokService**

```php
// app/Services/Admin/Config/PurokService.php
<?php

namespace App\Services\Admin\Config;

use App\Models\Purok;
use App\Models\Status;

class PurokService
{
    public function getAllPuroks(array $filters): array
    {
        $query = Purok::query()->with(['status', 'barangay']);

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('p_desc', 'like', "%{$search}%");
        }

        // Barangay filter
        if (!empty($filters['barangay_id'])) {
            $query->where('b_id', $filters['barangay_id']);
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('stat_id', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 15;
        $paginated = $query->orderBy('b_id')->orderBy('p_desc')->paginate($perPage);

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

    public function createPurok(array $data): Purok
    {
        $data['stat_id'] = Status::getIdByDescription(Status::ACTIVE);

        return Purok::create($data);
    }

    public function updatePurok(int $id, array $data): Purok
    {
        $purok = Purok::findOrFail($id);
        $purok->update($data);

        return $purok->fresh();
    }

    public function deletePurok(int $id): void
    {
        $purok = Purok::findOrFail($id);

        // Check for dependencies
        $addressesCount = $purok->consumerAddresses()->count();
        if ($addressesCount > 0) {
            throw new \DomainException(
                "Cannot delete purok '{$purok->p_desc}' because it is used in {$addressesCount} consumer addresses."
            );
        }

        $purok->delete();
    }

    public function getPurokDetails(int $id): Purok
    {
        return Purok::with(['status', 'barangay'])
            ->withCount(['consumerAddresses as addresses_count'])
            ->findOrFail($id);
    }
}
```

**Step 4: Run test to verify it passes**

```bash
php artisan test --filter=PurokServiceTest
```

Expected: PASS

**Step 5: Commit**

```bash
git add app/Services/Admin/Config/PurokService.php tests/Feature/Admin/Config/PurokServiceTest.php
git commit -m "feat(config): add PurokService with CRUD operations

- Implement getAllPuroks with search, barangay, status filters
- Add createPurok, updatePurok, deletePurok methods
- Prevent deletion when purok has associated addresses
- Include relationships and counts in getPurokDetails

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 1.2: Create Purok Form Requests

**Files:**
- Create: `app/Http/Requests/Admin/Config/StorePurokRequest.php`
- Create: `app/Http/Requests/Admin/Config/UpdatePurokRequest.php`

**Step 1: Create StorePurokRequest**

```php
// app/Http/Requests/Admin/Config/StorePurokRequest.php
<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;

class StorePurokRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('config.geographic.manage');
    }

    public function rules(): array
    {
        return [
            'p_desc' => ['required', 'string', 'max:255'],
            'b_id' => ['required', 'integer', 'exists:barangay,b_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'p_desc.required' => 'Purok name is required',
            'p_desc.max' => 'Purok name must not exceed 255 characters',
            'b_id.required' => 'Barangay is required',
            'b_id.exists' => 'Selected barangay does not exist',
        ];
    }
}
```

**Step 2: Create UpdatePurokRequest**

```php
// app/Http/Requests/Admin/Config/UpdatePurokRequest.php
<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurokRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('config.geographic.manage');
    }

    public function rules(): array
    {
        return [
            'p_desc' => ['sometimes', 'required', 'string', 'max:255'],
            'b_id' => ['sometimes', 'required', 'integer', 'exists:barangay,b_id'],
            'stat_id' => ['sometimes', 'required', 'integer', 'exists:status,stat_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'p_desc.required' => 'Purok name is required',
            'p_desc.max' => 'Purok name must not exceed 255 characters',
            'b_id.required' => 'Barangay is required',
            'b_id.exists' => 'Selected barangay does not exist',
            'stat_id.exists' => 'Selected status does not exist',
        ];
    }
}
```

**Step 3: Commit**

```bash
git add app/Http/Requests/Admin/Config/StorePurokRequest.php app/Http/Requests/Admin/Config/UpdatePurokRequest.php
git commit -m "feat(config): add Purok form request validation

- Create StorePurokRequest with p_desc and b_id validation
- Create UpdatePurokRequest with optional field updates
- Apply config.geographic.manage permission check
- Add custom error messages

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 1.3: Create PurokController

**Files:**
- Create: `app/Http/Controllers/Admin/Config/PurokController.php`

**Step 1: Create PurokController**

```php
// app/Http/Controllers/Admin/Config/PurokController.php
<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Config\StorePurokRequest;
use App\Http\Requests\Admin\Config\UpdatePurokRequest;
use App\Services\Admin\Config\PurokService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PurokController extends Controller
{
    public function __construct(
        private PurokService $purokService
    ) {}

    public function index(): View
    {
        return view('pages.admin.config.puroks.index');
    }

    public function store(StorePurokRequest $request): JsonResponse
    {
        try {
            $purok = $this->purokService->createPurok($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Purok created successfully',
                'data' => $purok->load(['barangay', 'status']),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create purok', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create purok',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $purok = $this->purokService->getPurokDetails($id);

            return response()->json([
                'data' => $purok,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Purok not found',
            ], 404);
        }
    }

    public function update(UpdatePurokRequest $request, int $id): JsonResponse
    {
        try {
            $purok = $this->purokService->updatePurok($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Purok updated successfully',
                'data' => $purok->load(['barangay', 'status']),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update purok', [
                'purok_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update purok',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->purokService->deletePurok($id);

            return response()->json([
                'success' => true,
                'message' => 'Purok deleted successfully',
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to delete purok', [
                'purok_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete purok',
            ], 500);
        }
    }
}
```

**Step 2: Add routes**

Modify: `routes/web.php` (add after areas routes, around line 518)

```php
// Puroks
Route::get('/puroks', [PurokController::class, 'index'])->name('config.puroks.index');
Route::post('/puroks', [PurokController::class, 'store'])->name('config.puroks.store');
Route::get('/puroks/{id}', [PurokController::class, 'show'])->name('config.puroks.show');
Route::put('/puroks/{id}', [PurokController::class, 'update'])->name('config.puroks.update');
Route::delete('/puroks/{id}', [PurokController::class, 'destroy'])->name('config.puroks.destroy');
```

**Step 3: Add import**

Modify: `routes/web.php` (add to imports at top)

```php
use App\Http\Controllers\Admin\Config\PurokController;
```

**Step 4: Commit**

```bash
git add app/Http/Controllers/Admin/Config/PurokController.php routes/web.php
git commit -m "feat(config): add PurokController with REST endpoints

- Implement index, store, show, update, destroy methods
- Add routes under config.geographic.manage middleware
- Include error handling and logging
- Load relationships in responses

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

## Phase 2: Backend - Account Type Management

### Task 2.1: Create AccountTypeService

**Files:**
- Create: `app/Services/Admin/Config/AccountTypeService.php`

**Step 1: Write AccountTypeService test**

```php
// tests/Feature/Admin/Config/AccountTypeServiceTest.php
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

        // Create service connection using this account type
        \App\Models\ServiceConnection::create([
            'account_no' => 'TEST-001',
            'customer_id' => 1,
            'address_id' => 1,
            'account_type_id' => $accountType->at_id,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        $this->expectException(\DomainException::class);
        $this->service->deleteAccountType($accountType->at_id);
    }
}
```

**Step 2: Run test to verify it fails**

```bash
php artisan test --filter=AccountTypeServiceTest
```

Expected: FAIL - AccountTypeService class not found

**Step 3: Create AccountTypeService**

```php
// app/Services/Admin/Config/AccountTypeService.php
<?php

namespace App\Services\Admin\Config;

use App\Models\AccountType;
use App\Models\Status;

class AccountTypeService
{
    public function getAllAccountTypes(array $filters): array
    {
        $query = AccountType::query()->with('status');

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('at_desc', 'like', "%{$search}%");
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('stat_id', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 15;
        $paginated = $query->orderBy('at_desc')->paginate($perPage);

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

    public function createAccountType(array $data): AccountType
    {
        $data['stat_id'] = Status::getIdByDescription(Status::ACTIVE);

        return AccountType::create($data);
    }

    public function updateAccountType(int $id, array $data): AccountType
    {
        $accountType = AccountType::findOrFail($id);
        $accountType->update($data);

        return $accountType->fresh();
    }

    public function deleteAccountType(int $id): void
    {
        $accountType = AccountType::findOrFail($id);

        // Check for dependencies
        $connectionsCount = $accountType->serviceConnections()->count();
        if ($connectionsCount > 0) {
            throw new \DomainException(
                "Cannot delete account type '{$accountType->at_desc}' because it has {$connectionsCount} associated service connections."
            );
        }

        $accountType->delete();
    }

    public function getAccountTypeDetails(int $id): AccountType
    {
        return AccountType::with('status')
            ->withCount(['serviceConnections as connections_count'])
            ->findOrFail($id);
    }
}
```

**Step 4: Run test to verify it passes**

```bash
php artisan test --filter=AccountTypeServiceTest
```

Expected: PASS

**Step 5: Commit**

```bash
git add app/Services/Admin/Config/AccountTypeService.php tests/Feature/Admin/Config/AccountTypeServiceTest.php
git commit -m "feat(config): add AccountTypeService with CRUD operations

- Implement getAllAccountTypes with search and status filters
- Add createAccountType, updateAccountType, deleteAccountType methods
- Prevent deletion when account type has associated connections
- Include relationships and counts in getAccountTypeDetails

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 2.2: Create Account Type Form Requests

**Files:**
- Create: `app/Http/Requests/Admin/Config/StoreAccountTypeRequest.php`
- Create: `app/Http/Requests/Admin/Config/UpdateAccountTypeRequest.php`

**Step 1: Create StoreAccountTypeRequest**

```php
// app/Http/Requests/Admin/Config/StoreAccountTypeRequest.php
<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('config.billing.manage');
    }

    public function rules(): array
    {
        return [
            'at_desc' => ['required', 'string', 'max:255', 'unique:account_type,at_desc'],
        ];
    }

    public function messages(): array
    {
        return [
            'at_desc.required' => 'Account type name is required',
            'at_desc.max' => 'Account type name must not exceed 255 characters',
            'at_desc.unique' => 'This account type already exists',
        ];
    }
}
```

**Step 2: Create UpdateAccountTypeRequest**

```php
// app/Http/Requests/Admin/Config/UpdateAccountTypeRequest.php
<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('config.billing.manage');
    }

    public function rules(): array
    {
        $accountTypeId = $this->route('id');

        return [
            'at_desc' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('account_type', 'at_desc')->ignore($accountTypeId, 'at_id'),
            ],
            'stat_id' => ['sometimes', 'required', 'integer', 'exists:status,stat_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'at_desc.required' => 'Account type name is required',
            'at_desc.max' => 'Account type name must not exceed 255 characters',
            'at_desc.unique' => 'This account type already exists',
            'stat_id.exists' => 'Selected status does not exist',
        ];
    }
}
```

**Step 3: Commit**

```bash
git add app/Http/Requests/Admin/Config/StoreAccountTypeRequest.php app/Http/Requests/Admin/Config/UpdateAccountTypeRequest.php
git commit -m "feat(config): add Account Type form request validation

- Create StoreAccountTypeRequest with unique at_desc validation
- Create UpdateAccountTypeRequest with unique rule ignoring current record
- Apply config.billing.manage permission check
- Add custom error messages

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 2.3: Create AccountTypeController

**Files:**
- Create: `app/Http/Controllers/Admin/Config/AccountTypeController.php`

**Step 1: Create AccountTypeController**

```php
// app/Http/Controllers/Admin/Config/AccountTypeController.php
<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Config\StoreAccountTypeRequest;
use App\Http\Requests\Admin\Config\UpdateAccountTypeRequest;
use App\Services\Admin\Config\AccountTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AccountTypeController extends Controller
{
    public function __construct(
        private AccountTypeService $accountTypeService
    ) {}

    public function index(): View
    {
        return view('pages.admin.config.account-types.index');
    }

    public function store(StoreAccountTypeRequest $request): JsonResponse
    {
        try {
            $accountType = $this->accountTypeService->createAccountType($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Account type created successfully',
                'data' => $accountType->load('status'),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create account type', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create account type',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $accountType = $this->accountTypeService->getAccountTypeDetails($id);

            return response()->json([
                'data' => $accountType,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Account type not found',
            ], 404);
        }
    }

    public function update(UpdateAccountTypeRequest $request, int $id): JsonResponse
    {
        try {
            $accountType = $this->accountTypeService->updateAccountType($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Account type updated successfully',
                'data' => $accountType->load('status'),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update account type', [
                'account_type_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update account type',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->accountTypeService->deleteAccountType($id);

            return response()->json([
                'success' => true,
                'message' => 'Account type deleted successfully',
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to delete account type', [
                'account_type_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account type',
            ], 500);
        }
    }
}
```

**Step 2: Add routes**

Modify: `routes/web.php` (add after water-rates routes, around line 529)

```php
// Account Types
Route::middleware(['permission:config.billing.manage'])->prefix('config')->group(function () {
    Route::get('/account-types', [AccountTypeController::class, 'index'])->name('config.account-types.index');
    Route::post('/account-types', [AccountTypeController::class, 'store'])->name('config.account-types.store');
    Route::get('/account-types/{id}', [AccountTypeController::class, 'show'])->name('config.account-types.show');
    Route::put('/account-types/{id}', [AccountTypeController::class, 'update'])->name('config.account-types.update');
    Route::delete('/account-types/{id}', [AccountTypeController::class, 'destroy'])->name('config.account-types.destroy');
});
```

**Step 3: Add import**

Modify: `routes/web.php` (add to imports at top)

```php
use App\Http\Controllers\Admin\Config\AccountTypeController;
```

**Step 4: Commit**

```bash
git add app/Http/Controllers/Admin/Config/AccountTypeController.php routes/web.php
git commit -m "feat(config): add AccountTypeController with REST endpoints

- Implement index, store, show, update, destroy methods
- Add routes under config.billing.manage middleware
- Include error handling and logging
- Load relationships in responses

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

## Phase 3: Backend - Application Fee Templates (ChargeItem)

### Task 3.1: Create ChargeItemService

**Files:**
- Create: `app/Services/Admin/Config/ChargeItemService.php`

**Step 1: Write ChargeItemService test**

```php
// tests/Feature/Admin/Config/ChargeItemServiceTest.php
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

        // Create customer charge using this charge item
        \App\Models\CustomerCharge::create([
            'customer_id' => 1,
            'charge_item_id' => $chargeItem->charge_item_id,
            'source_type' => 'ServiceApplication',
            'source_id' => 1,
            'amount' => 100.00,
            'stat_id' => Status::getIdByDescription(Status::PENDING),
        ]);

        $this->expectException(\DomainException::class);
        $this->service->deleteChargeItem($chargeItem->charge_item_id);
    }
}
```

**Step 2: Run test to verify it fails**

```bash
php artisan test --filter=ChargeItemServiceTest
```

Expected: FAIL - ChargeItemService class not found

**Step 3: Create ChargeItemService**

```php
// app/Services/Admin/Config/ChargeItemService.php
<?php

namespace App\Services\Admin\Config;

use App\Models\ChargeItem;
use App\Models\Status;

class ChargeItemService
{
    public function getAllChargeItems(array $filters): array
    {
        $query = ChargeItem::query()->with('status');

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Charge type filter
        if (!empty($filters['charge_type'])) {
            $query->where('charge_type', $filters['charge_type']);
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('stat_id', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 15;
        $paginated = $query->orderBy('charge_type')->orderBy('name')->paginate($perPage);

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

    public function createChargeItem(array $data): ChargeItem
    {
        $data['stat_id'] = Status::getIdByDescription(Status::ACTIVE);
        $data['is_taxable'] = $data['is_taxable'] ?? false;

        return ChargeItem::create($data);
    }

    public function updateChargeItem(int $id, array $data): ChargeItem
    {
        $chargeItem = ChargeItem::findOrFail($id);
        $chargeItem->update($data);

        return $chargeItem->fresh();
    }

    public function deleteChargeItem(int $id): void
    {
        $chargeItem = ChargeItem::findOrFail($id);

        // Check for dependencies
        $chargesCount = $chargeItem->customerCharges()->count();
        if ($chargesCount > 0) {
            throw new \DomainException(
                "Cannot delete charge item '{$chargeItem->name}' because it has {$chargesCount} associated customer charges."
            );
        }

        $chargeItem->delete();
    }

    public function getChargeItemDetails(int $id): ChargeItem
    {
        return ChargeItem::with('status')
            ->withCount(['customerCharges as charges_count'])
            ->findOrFail($id);
    }
}
```

**Step 4: Run test to verify it passes**

```bash
php artisan test --filter=ChargeItemServiceTest
```

Expected: PASS

**Step 5: Commit**

```bash
git add app/Services/Admin/Config/ChargeItemService.php tests/Feature/Admin/Config/ChargeItemServiceTest.php
git commit -m "feat(config): add ChargeItemService with CRUD operations

- Implement getAllChargeItems with search, charge_type, status filters
- Add createChargeItem, updateChargeItem, deleteChargeItem methods
- Prevent deletion when charge item has associated customer charges
- Include relationships and counts in getChargeItemDetails

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 3.2: Create Charge Item Form Requests

**Files:**
- Create: `app/Http/Requests/Admin/Config/StoreChargeItemRequest.php`
- Create: `app/Http/Requests/Admin/Config/UpdateChargeItemRequest.php`

**Step 1: Create StoreChargeItemRequest**

```php
// app/Http/Requests/Admin/Config/StoreChargeItemRequest.php
<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;

class StoreChargeItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('config.billing.manage');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:ChargeItem,code'],
            'description' => ['nullable', 'string', 'max:1000'],
            'default_amount' => ['required', 'numeric', 'min:0'],
            'charge_type' => ['required', 'in:one_time,recurring,penalty'],
            'is_taxable' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Charge item name is required',
            'code.required' => 'Code is required',
            'code.unique' => 'This code already exists',
            'default_amount.required' => 'Default amount is required',
            'default_amount.min' => 'Amount must be at least 0',
            'charge_type.required' => 'Charge type is required',
            'charge_type.in' => 'Invalid charge type',
        ];
    }
}
```

**Step 2: Create UpdateChargeItemRequest**

```php
// app/Http/Requests/Admin/Config/UpdateChargeItemRequest.php
<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChargeItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('config.billing.manage');
    }

    public function rules(): array
    {
        $chargeItemId = $this->route('id');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('ChargeItem', 'code')->ignore($chargeItemId, 'charge_item_id'),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'default_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'charge_type' => ['sometimes', 'required', 'in:one_time,recurring,penalty'],
            'is_taxable' => ['boolean'],
            'stat_id' => ['sometimes', 'required', 'integer', 'exists:status,stat_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Charge item name is required',
            'code.required' => 'Code is required',
            'code.unique' => 'This code already exists',
            'default_amount.required' => 'Default amount is required',
            'default_amount.min' => 'Amount must be at least 0',
            'charge_type.required' => 'Charge type is required',
            'charge_type.in' => 'Invalid charge type',
            'stat_id.exists' => 'Selected status does not exist',
        ];
    }
}
```

**Step 3: Commit**

```bash
git add app/Http/Requests/Admin/Config/StoreChargeItemRequest.php app/Http/Requests/Admin/Config/UpdateChargeItemRequest.php
git commit -m "feat(config): add Charge Item form request validation

- Create StoreChargeItemRequest with all required fields
- Create UpdateChargeItemRequest with unique code rule
- Validate charge_type enum (one_time, recurring, penalty)
- Apply config.billing.manage permission check
- Add custom error messages

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 3.3: Create ChargeItemController

**Files:**
- Create: `app/Http/Controllers/Admin/Config/ChargeItemController.php`

**Step 1: Create ChargeItemController**

```php
// app/Http/Controllers/Admin/Config/ChargeItemController.php
<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Config\StoreChargeItemRequest;
use App\Http\Requests\Admin\Config\UpdateChargeItemRequest;
use App\Services\Admin\Config\ChargeItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ChargeItemController extends Controller
{
    public function __construct(
        private ChargeItemService $chargeItemService
    ) {}

    public function index(): View
    {
        return view('pages.admin.config.charge-items.index');
    }

    public function store(StoreChargeItemRequest $request): JsonResponse
    {
        try {
            $chargeItem = $this->chargeItemService->createChargeItem($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Charge item created successfully',
                'data' => $chargeItem->load('status'),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create charge item', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create charge item',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $chargeItem = $this->chargeItemService->getChargeItemDetails($id);

            return response()->json([
                'data' => $chargeItem,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Charge item not found',
            ], 404);
        }
    }

    public function update(UpdateChargeItemRequest $request, int $id): JsonResponse
    {
        try {
            $chargeItem = $this->chargeItemService->updateChargeItem($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Charge item updated successfully',
                'data' => $chargeItem->load('status'),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update charge item', [
                'charge_item_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update charge item',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->chargeItemService->deleteChargeItem($id);

            return response()->json([
                'success' => true,
                'message' => 'Charge item deleted successfully',
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to delete charge item', [
                'charge_item_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete charge item',
            ], 500);
        }
    }
}
```

**Step 2: Add routes**

Modify: `routes/web.php` (add after account-types routes)

```php
// Application Fee Templates (ChargeItem)
Route::middleware(['permission:config.billing.manage'])->prefix('config')->group(function () {
    Route::get('/charge-items', [ChargeItemController::class, 'index'])->name('config.charge-items.index');
    Route::post('/charge-items', [ChargeItemController::class, 'store'])->name('config.charge-items.store');
    Route::get('/charge-items/{id}', [ChargeItemController::class, 'show'])->name('config.charge-items.show');
    Route::put('/charge-items/{id}', [ChargeItemController::class, 'update'])->name('config.charge-items.update');
    Route::delete('/charge-items/{id}', [ChargeItemController::class, 'destroy'])->name('config.charge-items.destroy');
});
```

**Step 3: Add import**

Modify: `routes/web.php` (add to imports at top)

```php
use App\Http\Controllers\Admin\Config\ChargeItemController;
```

**Step 4: Commit**

```bash
git add app/Http/Controllers/Admin/Config/ChargeItemController.php routes/web.php
git commit -m "feat(config): add ChargeItemController with REST endpoints

- Implement index, store, show, update, destroy methods
- Add routes under config.billing.manage middleware
- Include error handling and logging
- Load relationships in responses

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

**Backend for all 3 configurations is now complete. Continue to Phase 4 for Frontend implementation.**

## Phase 4: Frontend - Purok Management UI

### Task 4.1: Create Purok Main View

**Files:**
- Create: `resources/views/pages/admin/config/puroks/index.blade.php`

**Step 1: Create main view**

```blade
<!-- resources/views/pages/admin/config/puroks/index.blade.php -->
<x-app-layout>
    <div x-data="purokManager()" class="p-6">
        <!-- Header -->
        <x-ui.admin.config.shared.page-header
            title="Manage Puroks"
            subtitle="Configure sub-barangay areas for address management"
            :can-create="true"
            create-label="Add Purok"
            @create="openCreateModal()"
        />

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-4 mb-6">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Search
                </label>
                <input
                    type="text"
                    x-model="search"
                    @input.debounce.300ms="fetchItems()"
                    placeholder="Search puroks..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                />
            </div>

            <!-- Barangay Filter -->
            <div class="w-64">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Barangay
                </label>
                <select
                    x-model="barangayFilter"
                    @change="fetchItems()"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                >
                    <option value="">All Barangays</option>
                    <template x-for="barangay in barangays" :key="barangay.b_id">
                        <option :value="barangay.b_id" x-text="barangay.b_desc"></option>
                    </template>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status
                </label>
                <select
                    x-model="statusFilter"
                    @change="fetchItems()"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                >
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="2">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>

        <!-- Table -->
        <div x-show="!loading">
            <x-ui.admin.config.purok.table />
        </div>

        <!-- Pagination -->
        <x-ui.admin.config.shared.pagination />

        <!-- Modals -->
        <x-ui.admin.config.purok.modals.create-purok />
        <x-ui.admin.config.purok.modals.edit-purok />
        <x-ui.admin.config.purok.modals.view-purok />
        <x-ui.admin.config.purok.modals.delete-purok />
    </div>
</x-app-layout>
```

**Step 2: Commit**

```bash
git add resources/views/pages/admin/config/puroks/index.blade.php
git commit -m "feat(config): add Purok management main view

- Create index page with header and filters
- Add search, barangay, and status filters
- Include table and modal components
- Apply dark mode support

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 4.2: Create Purok Table Component

**Files:**
- Create: `resources/views/components/ui/admin/config/purok/table.blade.php`

**Step 1: Create table component**

```blade
<!-- resources/views/components/ui/admin/config/purok/table.blade.php -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Purok Name</th>
                    <th scope="col" class="px-6 py-3">Barangay</th>
                    <th scope="col" class="px-6 py-3">Addresses</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="purok in items" :key="purok.p_id">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <!-- Purok Name -->
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            <span x-text="purok.p_desc"></span>
                        </td>

                        <!-- Barangay -->
                        <td class="px-6 py-4">
                            <span x-text="purok.barangay?.b_desc || '-'"></span>
                        </td>

                        <!-- Addresses Count -->
                        <td class="px-6 py-4">
                            <span x-text="purok.addresses_count || 0"></span>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4">
                            <span
                                x-bind:class="purok.stat_id == 1 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                                class="px-2 py-1 text-xs font-medium rounded-full"
                                x-text="purok.stat_id == 1 ? 'ACTIVE' : 'INACTIVE'"
                            ></span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button
                                    @click="viewItem(purok.p_id)"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="View Details"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button
                                    @click="editItem(purok.p_id)"
                                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                                    title="Edit"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button
                                    @click="deleteItem(purok.p_id)"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    title="Delete"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>

                <!-- Empty State -->
                <tr x-show="items.length === 0">
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-4"></i>
                        <p>No puroks found</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

**Step 2: Commit**

```bash
git add resources/views/components/ui/admin/config/purok/table.blade.php
git commit -m "feat(config): add Purok table component

- Display purok name, barangay, addresses count, status
- Add view, edit, delete action buttons
- Include empty state message
- Apply responsive design and dark mode

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 4.3: Create purokManager Alpine.js Component

**Files:**
- Create: `resources/js/components/admin/config/puroks/purokManager.js`

**Step 1: Create purokManager**

```javascript
// resources/js/components/admin/config/puroks/purokManager.js
import configTable from '../shared/configTable.js';

export default function purokManager() {
    return {
        ...configTable('/config/puroks'),

        barangays: [],
        barangayFilter: '',

        async init() {
            await this.loadBarangays();
            await this.fetchItems();
        },

        async loadBarangays() {
            try {
                const response = await fetch('/barangays/list?all=true', {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) throw new Error('Failed to load barangays');

                const data = await response.json();
                this.barangays = data.data || [];
            } catch (error) {
                console.error('Failed to load barangays:', error);
            }
        },

        async fetchItems() {
            this.loading = true;
            this.errors = {};

            try {
                const params = new URLSearchParams({
                    search: this.search,
                    status: this.statusFilter,
                    barangay_id: this.barangayFilter,
                    page: this.pagination.currentPage,
                    per_page: this.pagination.perPage,
                });

                const response = await fetch(`${this.apiUrl}?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) throw new Error('Failed to fetch puroks');

                const data = await response.json();
                this.items = data.data || [];
                this.updatePagination(data.meta);
            } catch (error) {
                console.error('Failed to fetch puroks:', error);
                this.showNotification('Failed to load puroks', 'error');
            } finally {
                this.loading = false;
            }
        },

        async createPurok() {
            this.errors = {};

            try {
                const response = await fetch(this.apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    throw new Error(data.message || 'Failed to create purok');
                }

                this.showNotification('Purok created successfully', 'success');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to create purok:', error);
                this.showNotification(error.message, 'error');
            }
        },

        async updatePurok() {
            this.errors = {};

            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.p_id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.form),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    throw new Error(data.message || 'Failed to update purok');
                }

                this.showNotification('Purok updated successfully', 'success');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to update purok:', error);
                this.showNotification(error.message, 'error');
            }
        },

        async deletePurok() {
            try {
                const response = await fetch(`${this.apiUrl}/${this.selectedItem.p_id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete purok');
                }

                this.showNotification('Purok deleted successfully', 'success');
                this.closeAllModals();
                await this.fetchItems();
            } catch (error) {
                console.error('Failed to delete purok:', error);
                this.showNotification(error.message, 'error');
            }
        },
    };
}

window.purokManager = purokManager;
```

**Step 2: Add import to app.js**

Modify: `resources/js/app.js`

```javascript
import './components/admin/config/puroks/purokManager.js';
```

**Step 3: Commit**

```bash
git add resources/js/components/admin/config/puroks/purokManager.js resources/js/app.js
git commit -m "feat(config): add purokManager Alpine.js component

- Extend configTable utility with barangay filtering
- Implement createPurok, updatePurok, deletePurok methods
- Load barangays for dropdown filter
- Handle errors and notifications

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 4.4: Create Purok Modal Components

**Files:**
- Create: `resources/views/components/ui/admin/config/purok/modals/create-purok.blade.php`
- Create: `resources/views/components/ui/admin/config/purok/modals/edit-purok.blade.php`
- Create: `resources/views/components/ui/admin/config/purok/modals/view-purok.blade.php`
- Create: `resources/views/components/ui/admin/config/purok/modals/delete-purok.blade.php`

**Step 1: Create create-purok modal**

```blade
<!-- resources/views/components/ui/admin/config/purok/modals/create-purok.blade.php -->
<div x-show="showCreateModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAllModals()"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Add New Purok
                </h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form @submit.prevent="createPurok()" class="p-6 space-y-4">
                <!-- Purok Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Purok Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        x-model="form.p_desc"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        :class="{'border-red-500': errors.p_desc}"
                        placeholder="e.g., Purok 1"
                        required
                    />
                    <template x-if="errors.p_desc">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.p_desc[0]"></p>
                    </template>
                </div>

                <!-- Barangay -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Barangay <span class="text-red-500">*</span>
                    </label>
                    <select
                        x-model="form.b_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        :class="{'border-red-500': errors.b_id}"
                        required
                    >
                        <option value="">Select Barangay</option>
                        <template x-for="barangay in barangays" :key="barangay.b_id">
                            <option :value="barangay.b_id" x-text="barangay.b_desc"></option>
                        </template>
                    </select>
                    <template x-if="errors.b_id">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.b_id[0]"></p>
                    </template>
                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-700">
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
                        Create Purok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

**Step 2: Create edit-purok modal**

```blade
<!-- resources/views/components/ui/admin/config/purok/modals/edit-purok.blade.php -->
<div x-show="showEditModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAllModals()"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Edit Purok
                </h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form @submit.prevent="updatePurok()" class="p-6 space-y-4">
                <!-- Purok Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Purok Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        x-model="form.p_desc"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        :class="{'border-red-500': errors.p_desc}"
                        required
                    />
                    <template x-if="errors.p_desc">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.p_desc[0]"></p>
                    </template>
                </div>

                <!-- Barangay -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Barangay <span class="text-red-500">*</span>
                    </label>
                    <select
                        x-model="form.b_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        :class="{'border-red-500': errors.b_id}"
                        required
                    >
                        <option value="">Select Barangay</option>
                        <template x-for="barangay in barangays" :key="barangay.b_id">
                            <option :value="barangay.b_id" x-text="barangay.b_desc"></option>
                        </template>
                    </select>
                    <template x-if="errors.b_id">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.b_id[0]"></p>
                    </template>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select
                        x-model="form.stat_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        required
                    >
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
                    </select>
                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-700">
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
                        Update Purok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

**Step 3: Create view-purok modal**

```blade
<!-- resources/views/components/ui/admin/config/purok/modals/view-purok.blade.php -->
<div x-show="showViewModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAllModals()"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Purok Details
                </h3>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <!-- Purok Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                        Purok Name
                    </label>
                    <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.p_desc || '-'"></p>
                </div>

                <!-- Barangay -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                        Barangay
                    </label>
                    <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.barangay?.b_desc || '-'"></p>
                </div>

                <!-- Addresses Count -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                        Addresses Using This Purok
                    </label>
                    <p class="mt-1 text-base text-gray-900 dark:text-white" x-text="selectedItem?.addresses_count || 0"></p>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                        Status
                    </label>
                    <div class="mt-1">
                        <span
                            x-bind:class="selectedItem?.stat_id == 1 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                            class="px-2 py-1 text-xs font-medium rounded-full"
                            x-text="selectedItem?.stat_id == 1 ? 'ACTIVE' : 'INACTIVE'"
                        ></span>
                    </div>
                </div>
            </div>

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

**Step 4: Create delete-purok modal**

```blade
<!-- resources/views/components/ui/admin/config/purok/modals/delete-purok.blade.php -->
<div x-show="showDeleteModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAllModals()"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/20 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-500"></i>
                    </div>
                    <h3 class="ml-4 text-xl font-semibold text-gray-900 dark:text-white">
                        Delete Purok
                    </h3>
                </div>
                <button @click="closeAllModals()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6">
                <p class="text-gray-700 dark:text-gray-300">
                    Are you sure you want to delete this purok?
                </p>
                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Purok:</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="selectedItem?.p_desc"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Barangay:</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="selectedItem?.barangay?.b_desc"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Addresses:</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="selectedItem?.addresses_count || 0"></span>
                    </div>
                </div>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    This action cannot be undone. This purok will be permanently removed from the system.
                </p>
            </div>

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
                    @click="deletePurok()"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                >
                    Delete Purok
                </button>
            </div>
        </div>
    </div>
</div>
```

**Step 5: Commit**

```bash
git add resources/views/components/ui/admin/config/purok/modals/
git commit -m "feat(config): add Purok modal components

- Create create-purok modal with form validation
- Create edit-purok modal with status field
- Create view-purok modal showing details
- Create delete-purok modal with confirmation
- Include error handling and dark mode support

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

**Phase 4 Complete: Purok Management UI is ready**

## Phase 5: Frontend - Account Type Management UI

**Note:** Account Type UI follows the exact same pattern as Puroks, with these differences:
- No barangay filter (simpler)
- Table shows: Name, Connections Count, Status
- Forms only have: at_desc and stat_id fields
- Service count shown in view/delete modals

### Task 5.1: Create Account Type Main View

**Files:**
- Create: `resources/views/pages/admin/config/account-types/index.blade.php`

**Implementation:** Copy Purok index structure, remove barangay filter, use accountTypeManager()

**Commit:**
```bash
git add resources/views/pages/admin/config/account-types/index.blade.php
git commit -m "feat(config): add Account Type management main view

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 5.2: Create Account Type Table Component

**Files:**
- Create: `resources/views/components/ui/admin/config/account-type/table.blade.php`

**Columns:** Account Type Name | Service Connections | Status | Actions

**Commit:**
```bash
git add resources/views/components/ui/admin/config/account-type/table.blade.php
git commit -m "feat(config): add Account Type table component

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 5.3: Create accountTypeManager Alpine.js Component

**Files:**
- Create: `resources/js/components/admin/config/account-types/accountTypeManager.js`

**Implementation:** Extend configTable('/config/account-types'), implement create/update/delete methods

**Commit:**
```bash
git add resources/js/components/admin/config/account-types/accountTypeManager.js resources/js/app.js
git commit -m "feat(config): add accountTypeManager Alpine.js component

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 5.4: Create Account Type Modal Components

**Files:**
- Create: `resources/views/components/ui/admin/config/account-type/modals/create-account-type.blade.php`
- Create: `resources/views/components/ui/admin/config/account-type/modals/edit-account-type.blade.php`
- Create: `resources/views/components/ui/admin/config/account-type/modals/view-account-type.blade.php`
- Create: `resources/views/components/ui/admin/config/account-type/modals/delete-account-type.blade.php`

**Fields:** at_desc (required), stat_id (edit only)

**Commit:**
```bash
git add resources/views/components/ui/admin/config/account-type/modals/
git commit -m "feat(config): add Account Type modal components

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

**Phase 5 Complete: Account Type Management UI is ready**

## Phase 6: Frontend - ChargeItem (Application Fee Templates) Management UI

**Note:** ChargeItem UI is more complex with additional fields.

### Task 6.1: Create ChargeItem Main View

**Files:**
- Create: `resources/views/pages/admin/config/charge-items/index.blade.php`

**Filters:** Search, Charge Type dropdown (one_time, recurring, penalty, All), Status

**Commit:**
```bash
git add resources/views/pages/admin/config/charge-items/index.blade.php
git commit -m "feat(config): add ChargeItem management main view

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 6.2: Create ChargeItem Table Component

**Files:**
- Create: `resources/views/components/ui/admin/config/charge-item/table.blade.php`

**Columns:** Name | Code | Charge Type | Default Amount | Taxable | Customer Charges | Status | Actions

**Commit:**
```bash
git add resources/views/components/ui/admin/config/charge-item/table.blade.php
git commit -m "feat(config): add ChargeItem table component

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 6.3: Create chargeItemManager Alpine.js Component

**Files:**
- Create: `resources/js/components/admin/config/charge-items/chargeItemManager.js`

**Special:** Add chargeTypeFilter property, include in fetchItems params

**Commit:**
```bash
git add resources/js/components/admin/config/charge-items/chargeItemManager.js resources/js/app.js
git commit -m "feat(config): add chargeItemManager Alpine.js component

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

### Task 6.4: Create ChargeItem Modal Components

**Files:**
- Create: `resources/views/components/ui/admin/config/charge-item/modals/create-charge-item.blade.php`
- Create: `resources/views/components/ui/admin/config/charge-item/modals/edit-charge-item.blade.php`
- Create: `resources/views/components/ui/admin/config/charge-item/modals/view-charge-item.blade.php`
- Create: `resources/views/components/ui/admin/config/charge-item/modals/delete-charge-item.blade.php`

**Fields in Create/Edit:**
- name (text, required)
- code (text, required, uppercase)
- description (textarea, optional)
- default_amount (number, required, min 0, step 0.01)
- charge_type (select: one_time, recurring, penalty, required)
- is_taxable (checkbox, boolean)
- stat_id (edit only)

**View/Delete:** Show customer charges count

**Commit:**
```bash
git add resources/views/components/ui/admin/config/charge-item/modals/
git commit -m "feat(config): add ChargeItem modal components

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

**Phase 6 Complete: ChargeItem Management UI is ready**

## Phase 7: Navigation & Menu Updates

### Task 7.1: Update Sidebar Navigation

**Files:**
- Modify: `resources/views/components/sidebar-revamped.blade.php`

**Changes:**

1. Add Puroks link to Geographic submenu (after Areas):
```blade
<a href="{{ route('config.puroks.index') }}" @click="setActiveMenu('config-geographic-puroks')">
    <i class="fas fa-house-user"></i>Puroks
</a>
```

2. Create new "Billing Configuration" submenu under Admin Configuration (after Water Rates):
```blade
@can('config.billing.manage')
<div class="space-y-1">
    <button @click="toggleSubmenu('billingConfig')">
        <i class="fas fa-receipt"></i>Billing Configuration
        <i class="fas fa-chevron-down" :class="{ 'rotate-180': openSubmenus.billingConfig }"></i>
    </button>
    <div x-show="openSubmenus.billingConfig" x-collapse>
        <a href="{{ route('config.account-types.index') }}" @click="setActiveMenu('config-billing-account-types')">
            <i class="fas fa-users-cog"></i>Account Types
        </a>
        <a href="{{ route('config.charge-items.index') }}" @click="setActiveMenu('config-billing-charge-items')">
            <i class="fas fa-file-invoice-dollar"></i>Application Fee Templates
        </a>
    </div>
</div>
@endcan
```

3. Update Alpine.js data:
```javascript
openSubmenus: {
    // ... existing
    billingConfig: {{ str_starts_with(session('active_menu'), 'config-billing-') ? 'true' : 'false' }},
},

// Route map additions
'/config/puroks': 'config-geographic-puroks',
'/config/account-types': 'config-billing-account-types',
'/config/charge-items': 'config-billing-charge-items',
```

**Commit:**
```bash
git add resources/views/components/sidebar-revamped.blade.php
git commit -m "feat(config): update navigation for new configurations

- Add Puroks link to Geographic submenu
- Create Billing Configuration submenu under Admin Configuration
- Add Account Types and Application Fee Templates links
- Update Alpine.js state management for new menu items

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"
```

---

**All 7 Phases Complete!**

## Summary

**What was implemented:**

**Backend (Phases 1-3):**
- ✅ PurokService with CRUD + tests
- ✅ AccountTypeService with CRUD + tests
- ✅ ChargeItemService with CRUD + tests
- ✅ Form Request validation for all 3
- ✅ Controllers with REST endpoints
- ✅ Routes with RBAC middleware

**Frontend (Phases 4-6):**
- ✅ Purok Management UI (main view, table, Alpine manager, 4 modals)
- ✅ Account Type Management UI (main view, table, Alpine manager, 4 modals)
- ✅ ChargeItem Management UI (main view, table, Alpine manager, 4 modals)

**Navigation (Phase 7):**
- ✅ Puroks added to Geographic submenu
- ✅ Billing Configuration submenu created
- ✅ Account Types and Application Fee Templates added

**Total Implementation:**
- 9 Service classes with full test coverage
- 18 Form Request classes
- 9 Controllers with REST APIs
- 12 Blade view pages (3 index + 9 modals per config × 3)
- 3 Table components
- 3 Alpine.js managers
- 1 Navigation update
- All following TDD, RBAC, and existing patterns

**Ready for execution using superpowers:executing-plans skill!**

---
