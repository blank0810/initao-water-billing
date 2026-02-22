# Customer Status-Based Validation Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Enforce customer status (PENDING, ACTIVE, INACTIVE) as a gate that controls which actions can be performed on a customer across the service layer, UI, and route middleware.

**Architecture:** Create a `CustomerStatusService` that centralizes all status validation logic with clear methods like `assertCustomerIsActive()`. Integrate these checks into existing services (ServiceApplicationService, PaymentService, CustomerService). Add a `CheckCustomerStatus` middleware for route-level protection. Conditionally render action buttons in Blade views. Fully implement the stubbed `CustomerApprovalController` with a backing service for approve/decline/restore/reactivate flows.

**Tech Stack:** Laravel 12 (PHP 8.2), Pest PHP, Blade + Alpine.js, Tailwind CSS, Flowbite

---

## Business Rules Summary

| Customer Status | Allowed Actions | Blocked Actions |
|----------------|----------------|-----------------|
| **ACTIVE** | Everything: create applications, payments, connections, edit, delete | None |
| **PENDING** | View only, approve/decline | Create applications, payments, connections, edit, delete |
| **INACTIVE** | View, pay existing bills, reactivate | Create new applications, create new connections, edit |

---

### Task 1: Create CustomerStatusService

**Files:**
- Create: `app/Services/Customers/CustomerStatusService.php`
- Test: `tests/Feature/Services/Customer/CustomerStatusServiceTest.php`

**Step 1: Write the failing tests**

```php
<?php

namespace Tests\Feature\Services\Customer;

use App\Models\Customer;
use App\Models\Status;
use App\Services\Customers\CustomerStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Status::create(['stat_id' => 1, 'stat_desc' => 'ACTIVE']);
    Status::create(['stat_id' => 2, 'stat_desc' => 'INACTIVE']);
    Status::create(['stat_id' => 3, 'stat_desc' => 'PENDING']);
    Status::create(['stat_id' => 4, 'stat_desc' => 'VERIFIED']);
    Status::create(['stat_id' => 5, 'stat_desc' => 'PAID']);
    Status::create(['stat_id' => 6, 'stat_desc' => 'SCHEDULED']);
    Status::create(['stat_id' => 7, 'stat_desc' => 'CONNECTED']);
    Status::create(['stat_id' => 8, 'stat_desc' => 'REJECTED']);
    Status::create(['stat_id' => 9, 'stat_desc' => 'CANCELLED']);
    Status::create(['stat_id' => 10, 'stat_desc' => 'SUSPENDED']);
    Status::create(['stat_id' => 11, 'stat_desc' => 'DISCONNECTED']);
    Status::create(['stat_id' => 12, 'stat_desc' => 'OVERDUE']);

    $this->service = app(CustomerStatusService::class);
});

test('assertCustomerCanCreateApplication passes for ACTIVE customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'JOHN',
        'cust_last_name' => 'DOE',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-001',
    ]);

    // Should not throw
    $this->service->assertCustomerCanCreateApplication($customer);
    expect(true)->toBeTrue();
});

test('assertCustomerCanCreateApplication throws for PENDING customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'JANE',
        'cust_last_name' => 'DOE',
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-002',
    ]);

    $this->service->assertCustomerCanCreateApplication($customer);
})->throws(\Exception::class, 'Customer must be ACTIVE to create a service application. Current status: PENDING.');

test('assertCustomerCanCreateApplication throws for INACTIVE customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'BOB',
        'cust_last_name' => 'SMITH',
        'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-003',
    ]);

    $this->service->assertCustomerCanCreateApplication($customer);
})->throws(\Exception::class, 'Customer must be ACTIVE to create a service application. Current status: INACTIVE.');

test('assertCustomerCanProcessPayment passes for ACTIVE customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'JOHN',
        'cust_last_name' => 'DOE',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-004',
    ]);

    $this->service->assertCustomerCanProcessPayment($customer);
    expect(true)->toBeTrue();
});

test('assertCustomerCanProcessPayment passes for INACTIVE customer paying existing bills', function () {
    $customer = Customer::create([
        'cust_first_name' => 'OLD',
        'cust_last_name' => 'CUSTOMER',
        'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-005',
    ]);

    // INACTIVE can still pay existing bills
    $this->service->assertCustomerCanProcessPayment($customer);
    expect(true)->toBeTrue();
});

test('assertCustomerCanProcessPayment throws for PENDING customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'NEW',
        'cust_last_name' => 'PERSON',
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-006',
    ]);

    $this->service->assertCustomerCanProcessPayment($customer);
})->throws(\Exception::class, 'Customer must be ACTIVE or INACTIVE to process payments. Current status: PENDING.');

test('assertCustomerCanEdit passes for ACTIVE customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'EDIT',
        'cust_last_name' => 'ME',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-007',
    ]);

    $this->service->assertCustomerCanEdit($customer);
    expect(true)->toBeTrue();
});

test('assertCustomerCanEdit throws for PENDING customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'PENDING',
        'cust_last_name' => 'EDIT',
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-008',
    ]);

    $this->service->assertCustomerCanEdit($customer);
})->throws(\Exception::class, 'Customer must be ACTIVE to edit. Current status: PENDING.');

test('assertCustomerCanEdit throws for INACTIVE customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'INACTIVE',
        'cust_last_name' => 'EDIT',
        'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-009',
    ]);

    $this->service->assertCustomerCanEdit($customer);
})->throws(\Exception::class, 'Customer must be ACTIVE to edit. Current status: INACTIVE.');

test('getCustomerStatusDescription returns correct description', function () {
    $customer = Customer::create([
        'cust_first_name' => 'STATUS',
        'cust_last_name' => 'CHECK',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-010',
    ]);

    expect($this->service->getCustomerStatusDescription($customer))->toBe('ACTIVE');
});

test('getCustomerAllowedActions returns full actions for ACTIVE customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'FULL',
        'cust_last_name' => 'ACCESS',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-011',
    ]);

    $actions = $this->service->getCustomerAllowedActions($customer);

    expect($actions)->toContain('create_application')
        ->toContain('process_payment')
        ->toContain('edit')
        ->toContain('delete');
});

test('getCustomerAllowedActions returns view-only for PENDING customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'VIEW',
        'cust_last_name' => 'ONLY',
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-012',
    ]);

    $actions = $this->service->getCustomerAllowedActions($customer);

    expect($actions)->toContain('view')
        ->toContain('approve')
        ->toContain('decline')
        ->not->toContain('create_application')
        ->not->toContain('process_payment')
        ->not->toContain('edit')
        ->not->toContain('delete');
});

test('getCustomerAllowedActions returns limited actions for INACTIVE customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'LIMITED',
        'cust_last_name' => 'ACCESS',
        'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-013',
    ]);

    $actions = $this->service->getCustomerAllowedActions($customer);

    expect($actions)->toContain('view')
        ->toContain('process_payment')
        ->toContain('reactivate')
        ->not->toContain('create_application')
        ->not->toContain('edit')
        ->not->toContain('delete');
});
```

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/Services/Customer/CustomerStatusServiceTest.php`
Expected: FAIL — class `CustomerStatusService` not found

**Step 3: Write the implementation**

```php
<?php

namespace App\Services\Customers;

use App\Models\Customer;
use App\Models\Status;

class CustomerStatusService
{
    /**
     * Assert customer is ACTIVE before creating a service application.
     *
     * @throws \Exception
     */
    public function assertCustomerCanCreateApplication(Customer $customer): void
    {
        $status = $this->getCustomerStatusDescription($customer);

        if ($status !== Status::ACTIVE) {
            throw new \Exception("Customer must be ACTIVE to create a service application. Current status: {$status}.");
        }
    }

    /**
     * Assert customer can process payments.
     * ACTIVE and INACTIVE customers can pay (INACTIVE can pay existing bills).
     * PENDING customers cannot.
     *
     * @throws \Exception
     */
    public function assertCustomerCanProcessPayment(Customer $customer): void
    {
        $status = $this->getCustomerStatusDescription($customer);

        if (! in_array($status, [Status::ACTIVE, Status::INACTIVE])) {
            throw new \Exception("Customer must be ACTIVE or INACTIVE to process payments. Current status: {$status}.");
        }
    }

    /**
     * Assert customer can be edited. Only ACTIVE customers.
     *
     * @throws \Exception
     */
    public function assertCustomerCanEdit(Customer $customer): void
    {
        $status = $this->getCustomerStatusDescription($customer);

        if ($status !== Status::ACTIVE) {
            throw new \Exception("Customer must be ACTIVE to edit. Current status: {$status}.");
        }
    }

    /**
     * Assert customer can be deleted. Only ACTIVE customers (with further checks in CustomerService).
     *
     * @throws \Exception
     */
    public function assertCustomerCanDelete(Customer $customer): void
    {
        $status = $this->getCustomerStatusDescription($customer);

        if ($status !== Status::ACTIVE) {
            throw new \Exception("Customer must be ACTIVE to delete. Current status: {$status}.");
        }
    }

    /**
     * Get the status description string for a customer.
     */
    public function getCustomerStatusDescription(Customer $customer): string
    {
        if (! $customer->relationLoaded('status')) {
            $customer->load('status');
        }

        return $customer->status?->stat_desc ?? 'UNKNOWN';
    }

    /**
     * Get allowed actions for a customer based on their status.
     * Used by UI to conditionally show/hide action buttons.
     *
     * @return string[]
     */
    public function getCustomerAllowedActions(Customer $customer): array
    {
        $status = $this->getCustomerStatusDescription($customer);

        return match ($status) {
            Status::ACTIVE => [
                'view',
                'edit',
                'delete',
                'create_application',
                'process_payment',
            ],
            Status::PENDING => [
                'view',
                'approve',
                'decline',
            ],
            Status::INACTIVE => [
                'view',
                'process_payment',
                'reactivate',
            ],
            default => ['view'],
        };
    }
}
```

**Step 4: Run tests to verify they pass**

Run: `php artisan test tests/Feature/Services/Customer/CustomerStatusServiceTest.php`
Expected: ALL PASS

**Step 5: Commit**

```bash
git add app/Services/Customers/CustomerStatusService.php tests/Feature/Services/Customer/CustomerStatusServiceTest.php
git commit -m "feat(customer): add CustomerStatusService for status-based validation"
```

---

### Task 2: Implement CustomerApprovalService (replace stub controller logic)

**Files:**
- Create: `app/Services/Customers/CustomerApprovalService.php`
- Modify: `app/Http/Controllers/Customer/CustomerApprovalController.php`
- Test: `tests/Feature/Services/Customer/CustomerApprovalServiceTest.php`

**Step 1: Write the failing tests**

```php
<?php

namespace Tests\Feature\Services\Customer;

use App\Models\Customer;
use App\Models\Status;
use App\Services\Customers\CustomerApprovalService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Status::create(['stat_id' => 1, 'stat_desc' => 'ACTIVE']);
    Status::create(['stat_id' => 2, 'stat_desc' => 'INACTIVE']);
    Status::create(['stat_id' => 3, 'stat_desc' => 'PENDING']);

    $this->service = app(CustomerApprovalService::class);
});

test('approveCustomer transitions PENDING customer to ACTIVE', function () {
    $customer = Customer::create([
        'cust_first_name' => 'APPROVE',
        'cust_last_name' => 'ME',
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-100',
    ]);

    $result = $this->service->approveCustomer($customer->cust_id);

    expect($result->stat_id)->toBe(Status::getIdByDescription(Status::ACTIVE));
    expect($result->fresh()->stat_id)->toBe(Status::getIdByDescription(Status::ACTIVE));
});

test('approveCustomer throws if customer is already ACTIVE', function () {
    $customer = Customer::create([
        'cust_first_name' => 'ALREADY',
        'cust_last_name' => 'ACTIVE',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-101',
    ]);

    $this->service->approveCustomer($customer->cust_id);
})->throws(\Exception::class, 'Only PENDING customers can be approved.');

test('declineCustomer transitions PENDING customer to INACTIVE', function () {
    $customer = Customer::create([
        'cust_first_name' => 'DECLINE',
        'cust_last_name' => 'ME',
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-102',
    ]);

    $result = $this->service->declineCustomer($customer->cust_id, 'Incomplete documents');

    expect($result->stat_id)->toBe(Status::getIdByDescription(Status::INACTIVE));
});

test('declineCustomer throws if customer is not PENDING', function () {
    $customer = Customer::create([
        'cust_first_name' => 'NOT',
        'cust_last_name' => 'PENDING',
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-103',
    ]);

    $this->service->declineCustomer($customer->cust_id, 'Some reason');
})->throws(\Exception::class, 'Only PENDING customers can be declined.');

test('restoreCustomer transitions INACTIVE customer back to PENDING', function () {
    $customer = Customer::create([
        'cust_first_name' => 'RESTORE',
        'cust_last_name' => 'ME',
        'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-104',
    ]);

    $result = $this->service->restoreCustomer($customer->cust_id);

    expect($result->stat_id)->toBe(Status::getIdByDescription(Status::PENDING));
});

test('reactivateCustomer transitions INACTIVE customer to ACTIVE', function () {
    $customer = Customer::create([
        'cust_first_name' => 'REACTIVATE',
        'cust_last_name' => 'ME',
        'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-105',
    ]);

    $result = $this->service->reactivateCustomer($customer->cust_id);

    expect($result->stat_id)->toBe(Status::getIdByDescription(Status::ACTIVE));
});

test('reactivateCustomer throws if customer is not INACTIVE', function () {
    $customer = Customer::create([
        'cust_first_name' => 'NOT',
        'cust_last_name' => 'INACTIVE',
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-106',
    ]);

    $this->service->reactivateCustomer($customer->cust_id);
})->throws(\Exception::class, 'Only INACTIVE customers can be reactivated.');
```

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/Services/Customer/CustomerApprovalServiceTest.php`
Expected: FAIL — class `CustomerApprovalService` not found

**Step 3: Write the CustomerApprovalService**

```php
<?php

namespace App\Services\Customers;

use App\Models\Customer;
use App\Models\Status;

class CustomerApprovalService
{
    /**
     * Approve a PENDING customer — transitions to ACTIVE.
     *
     * @throws \Exception
     */
    public function approveCustomer(int $customerId): Customer
    {
        $customer = Customer::findOrFail($customerId);

        if ($customer->stat_id !== Status::getIdByDescription(Status::PENDING)) {
            throw new \Exception('Only PENDING customers can be approved.');
        }

        $customer->update([
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        return $customer->fresh('status');
    }

    /**
     * Decline a PENDING customer — transitions to INACTIVE.
     *
     * @throws \Exception
     */
    public function declineCustomer(int $customerId, string $reason): Customer
    {
        $customer = Customer::findOrFail($customerId);

        if ($customer->stat_id !== Status::getIdByDescription(Status::PENDING)) {
            throw new \Exception('Only PENDING customers can be declined.');
        }

        $customer->update([
            'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        ]);

        return $customer->fresh('status');
    }

    /**
     * Restore a declined (INACTIVE) customer back to PENDING for re-review.
     *
     * @throws \Exception
     */
    public function restoreCustomer(int $customerId): Customer
    {
        $customer = Customer::findOrFail($customerId);

        if ($customer->stat_id !== Status::getIdByDescription(Status::INACTIVE)) {
            throw new \Exception('Only INACTIVE customers can be restored to PENDING.');
        }

        $customer->update([
            'stat_id' => Status::getIdByDescription(Status::PENDING),
        ]);

        return $customer->fresh('status');
    }

    /**
     * Reactivate an INACTIVE customer directly to ACTIVE.
     *
     * @throws \Exception
     */
    public function reactivateCustomer(int $customerId): Customer
    {
        $customer = Customer::findOrFail($customerId);

        if ($customer->stat_id !== Status::getIdByDescription(Status::INACTIVE)) {
            throw new \Exception('Only INACTIVE customers can be reactivated.');
        }

        $customer->update([
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        return $customer->fresh('status');
    }
}
```

**Step 4: Rewrite the CustomerApprovalController to use the service (thin controller)**

Replace the entire content of `app/Http/Controllers/Customer/CustomerApprovalController.php` with:

```php
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customers\CustomerApprovalService;
use Illuminate\Http\Request;

class CustomerApprovalController extends Controller
{
    public function __construct(
        protected CustomerApprovalService $approvalService
    ) {}

    public function approve(Request $request)
    {
        $request->validate(['customer_id' => 'required|integer|exists:customer,cust_id']);

        try {
            $customer = $this->approvalService->approveCustomer($request->input('customer_id'));

            return redirect()->route('service.connection', ['customer_id' => $customer->cust_id])
                ->with('success', 'Customer approved successfully. Please proceed with service connection.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function decline(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer|exists:customer,cust_id',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->approvalService->declineCustomer(
                $request->input('customer_id'),
                $request->input('reason')
            );

            return response()->json([
                'success' => true,
                'message' => 'Customer application declined successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function restore(Request $request)
    {
        $request->validate(['customer_id' => 'required|integer|exists:customer,cust_id']);

        try {
            $this->approvalService->restoreCustomer($request->input('customer_id'));

            return response()->json([
                'success' => true,
                'message' => 'Customer application restored to approval queue.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
```

**Step 5: Run tests to verify they pass**

Run: `php artisan test tests/Feature/Services/Customer/CustomerApprovalServiceTest.php`
Expected: ALL PASS

**Step 6: Commit**

```bash
git add app/Services/Customers/CustomerApprovalService.php app/Http/Controllers/Customer/CustomerApprovalController.php tests/Feature/Services/Customer/CustomerApprovalServiceTest.php
git commit -m "feat(customer): implement CustomerApprovalService and wire up controller"
```

---

### Task 3: Add reactivate route and controller method

**Files:**
- Modify: `routes/web.php` (add reactivate route near line 162)
- Modify: `app/Http/Controllers/Customer/CustomerApprovalController.php` (add reactivate method)

**Step 1: Add reactivate method to CustomerApprovalController**

Add this method after the `restore()` method:

```php
public function reactivate(Request $request)
{
    $request->validate(['customer_id' => 'required|integer|exists:customer,cust_id']);

    try {
        $customer = $this->approvalService->reactivateCustomer($request->input('customer_id'));

        return response()->json([
            'success' => true,
            'message' => 'Customer reactivated successfully.',
            'data' => $customer->load('status'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 422);
    }
}
```

**Step 2: Add route in `routes/web.php`**

Find the customer approval routes (around line 160-162) and add after `Route::post('/customer/restore', ...)`:

```php
Route::post('/customer/reactivate', [CustomerApprovalController::class, 'reactivate'])->name('customer.reactivate');
```

**Step 3: Run existing tests to verify nothing broke**

Run: `php artisan test`
Expected: ALL PASS

**Step 4: Commit**

```bash
git add routes/web.php app/Http/Controllers/Customer/CustomerApprovalController.php
git commit -m "feat(customer): add reactivate route and controller endpoint"
```

---

### Task 4: Integrate status checks into ServiceApplicationService

**Files:**
- Modify: `app/Services/ServiceApplication/ServiceApplicationService.php` (line ~39, inject CustomerStatusService and add check)
- Test: `tests/Feature/Services/Customer/ServiceApplicationStatusCheckTest.php`

**Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature\Services\Customer;

use App\Models\Customer;
use App\Models\Status;
use App\Services\ServiceApplication\ServiceApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Status::create(['stat_id' => 1, 'stat_desc' => 'ACTIVE']);
    Status::create(['stat_id' => 2, 'stat_desc' => 'INACTIVE']);
    Status::create(['stat_id' => 3, 'stat_desc' => 'PENDING']);
    Status::create(['stat_id' => 4, 'stat_desc' => 'VERIFIED']);
    Status::create(['stat_id' => 5, 'stat_desc' => 'PAID']);

    // Create required database records for address creation
    \DB::table('province')->insert(['prov_id' => 1, 'prov_desc' => 'Misamis Oriental']);
    \DB::table('town')->insert(['t_id' => 1, 't_desc' => 'Initao', 'prov_id' => 1]);
    \DB::table('barangay')->insert(['b_id' => 1, 'b_desc' => 'Poblacion', 'b_code' => 'POBA', 't_id' => 1]);
    \DB::table('purok')->insert(['p_id' => 1, 'p_desc' => 'Purok 1', 'b_id' => 1]);

    $this->service = app(ServiceApplicationService::class);
});

test('createApplication throws for PENDING existing customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'PENDING',
        'cust_last_name' => 'CUSTOMER',
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-200',
    ]);

    $this->service->createApplication(
        'existing',
        ['customerId' => $customer->cust_id],
        ['barangay' => 1, 'purok' => 1, 'landmark' => 'Test'],
        1
    );
})->throws(\Exception::class, 'Customer must be ACTIVE to create a service application.');

test('createApplication throws for INACTIVE existing customer', function () {
    $customer = Customer::create([
        'cust_first_name' => 'INACTIVE',
        'cust_last_name' => 'CUSTOMER',
        'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-TEST-201',
    ]);

    $this->service->createApplication(
        'existing',
        ['customerId' => $customer->cust_id],
        ['barangay' => 1, 'purok' => 1, 'landmark' => 'Test'],
        1
    );
})->throws(\Exception::class, 'Customer must be ACTIVE to create a service application.');
```

**Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/Services/Customer/ServiceApplicationStatusCheckTest.php`
Expected: FAIL — no exception thrown (the check doesn't exist yet)

**Step 3: Modify ServiceApplicationService**

In `app/Services/ServiceApplication/ServiceApplicationService.php`:

1. Add import at top (after existing imports):
```php
use App\Services\Customers\CustomerStatusService;
```

2. Modify the constructor (line ~21) to inject CustomerStatusService:
```php
public function __construct(
    protected ApplicationChargeService $chargeService,
    protected LedgerService $ledgerService,
    protected PaymentService $paymentService,
    protected NotificationService $notificationService,
    protected CustomerStatusService $customerStatusService
) {}
```

3. In `createApplication()` method, add the status check right after the existing customer lookup (after line 111, inside the `else` block for existing customers):

Find this code:
```php
} else {
    // Find existing customer - DO NOT update their home address
    // The customer's ca_id (home address) remains unchanged
    // Only the service application gets the new service address
    $customer = Customer::findOrFail($customerData['customerId'] ?? $customerData['customer_id'] ?? $customerData['id']);
}
```

Replace with:
```php
} else {
    // Find existing customer - DO NOT update their home address
    // The customer's ca_id (home address) remains unchanged
    // Only the service application gets the new service address
    $customer = Customer::findOrFail($customerData['customerId'] ?? $customerData['customer_id'] ?? $customerData['id']);

    // Validate customer status before creating application
    $this->customerStatusService->assertCustomerCanCreateApplication($customer);
}
```

**Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/Services/Customer/ServiceApplicationStatusCheckTest.php`
Expected: ALL PASS

**Step 5: Run full test suite**

Run: `php artisan test`
Expected: ALL PASS

**Step 6: Commit**

```bash
git add app/Services/ServiceApplication/ServiceApplicationService.php tests/Feature/Services/Customer/ServiceApplicationStatusCheckTest.php
git commit -m "feat(application): block PENDING/INACTIVE customers from creating applications"
```

---

### Task 5: Integrate status checks into PaymentService

**Files:**
- Modify: `app/Services/Payment/PaymentService.php` (add customer status check before processing payments)

**Step 1: Modify PaymentService to inject and use CustomerStatusService**

In `app/Services/Payment/PaymentService.php`:

1. Add import:
```php
use App\Services\Customers\CustomerStatusService;
```

2. Modify the constructor:
```php
public function __construct(
    protected ApplicationChargeService $chargeService,
    protected LedgerService $ledgerService,
    protected NotificationService $notificationService,
    protected CustomerStatusService $customerStatusService
) {}
```

3. In `processApplicationPayment()` (line ~63), after the application is loaded but before the status check, add customer validation:

Find this code (line ~63-64):
```php
$application = ServiceApplication::with('customer')->findOrFail($applicationId);

// Validate application is in VERIFIED status
```

Replace with:
```php
$application = ServiceApplication::with('customer')->findOrFail($applicationId);

// Validate customer status
$this->customerStatusService->assertCustomerCanProcessPayment($application->customer);

// Validate application is in VERIFIED status
```

4. In `processWaterBillPayment()` (around line ~400-406), after the customer is resolved from the connection, add validation:

Find this code:
```php
$connection = $bill->serviceConnection;
$customer = $connection->customer;
$customerForNotification = $customer;

if (! $customer) {
    throw new \Exception('No customer associated with this connection.');
}
```

Replace with:
```php
$connection = $bill->serviceConnection;
$customer = $connection->customer;
$customerForNotification = $customer;

if (! $customer) {
    throw new \Exception('No customer associated with this connection.');
}

// Validate customer status
$this->customerStatusService->assertCustomerCanProcessPayment($customer);
```

5. In `processConnectionPayment()` (around line ~634-639), same pattern after customer is resolved:

Find:
```php
$customer = $connection->customer;
$customerForNotification = $customer;

if (! $customer) {
    throw new \Exception('No customer associated with this connection.');
}
```

Replace with:
```php
$customer = $connection->customer;
$customerForNotification = $customer;

if (! $customer) {
    throw new \Exception('No customer associated with this connection.');
}

// Validate customer status
$this->customerStatusService->assertCustomerCanProcessPayment($customer);
```

**Step 2: Run full test suite to verify nothing broke**

Run: `php artisan test`
Expected: ALL PASS (PENDING customers don't exist in current payment tests)

**Step 3: Commit**

```bash
git add app/Services/Payment/PaymentService.php
git commit -m "feat(payment): add customer status validation before processing payments"
```

---

### Task 6: Integrate status checks into CustomerService (edit/delete)

**Files:**
- Modify: `app/Services/Customers/CustomerService.php`

**Step 1: Modify CustomerService to inject and use CustomerStatusService**

1. Add import:
```php
use App\Services\Customers\CustomerStatusService;
```

2. Add constructor and inject:

The CustomerService currently has no constructor. Add one at the top of the class (after line 17):
```php
public function __construct(
    protected CustomerStatusService $customerStatusService
) {}
```

3. In `updateCustomer()` method (line ~453), add status check before update:

Find:
```php
public function updateCustomer(int $customerId, array $data): Customer
{
    $customer = Customer::find($customerId);

    if (! $customer) {
        throw new \Exception('Customer not found');
    }

    $customer->update([
```

Replace with:
```php
public function updateCustomer(int $customerId, array $data): Customer
{
    $customer = Customer::find($customerId);

    if (! $customer) {
        throw new \Exception('Customer not found');
    }

    $this->customerStatusService->assertCustomerCanEdit($customer);

    $customer->update([
```

4. In `deleteCustomer()` method (line ~476), add status check before deletion:

Find:
```php
public function deleteCustomer(int $customerId): bool
{
    return DB::transaction(function () use ($customerId) {
        $customer = Customer::find($customerId);

        if (! $customer) {
            throw new \Exception('Customer not found');
        }

        // Delete related service applications
```

Replace with:
```php
public function deleteCustomer(int $customerId): bool
{
    return DB::transaction(function () use ($customerId) {
        $customer = Customer::find($customerId);

        if (! $customer) {
            throw new \Exception('Customer not found');
        }

        $this->customerStatusService->assertCustomerCanDelete($customer);

        // Delete related service applications
```

5. In `searchCustomers()` method (line ~259), tighten the filter to only return ACTIVE customers:

Find:
```php
->whereHas('status', function (Builder $q) {
    $q->where('stat_desc', '!=', 'INACTIVE');
})
```

Replace with:
```php
->whereHas('status', function (Builder $q) {
    $q->where('stat_desc', Status::ACTIVE);
})
```

**Step 2: Run full test suite**

Run: `php artisan test`
Expected: ALL PASS

**Step 3: Commit**

```bash
git add app/Services/Customers/CustomerService.php
git commit -m "feat(customer): enforce status checks on edit, delete, and search"
```

---

### Task 7: Add allowed_actions to customer API response

**Files:**
- Modify: `app/Services/Customers/CustomerService.php` — `getCustomerDetails()` / detail response builder

**Step 1: Add allowed_actions to the customer details API response**

In `app/Services/Customers/CustomerService.php`, find where the customer detail response array is built (the method that returns data for `/api/customer/{id}/details`). Look for the section that builds `'account_status'` (around line 585). Add alongside it:

```php
'allowed_actions' => $this->customerStatusService->getCustomerAllowedActions($customer),
```

**Step 2: Run existing tests to make sure nothing broke**

Run: `php artisan test`
Expected: ALL PASS

**Step 3: Commit**

```bash
git add app/Services/Customers/CustomerService.php
git commit -m "feat(customer): expose allowed_actions in customer details API response"
```

---

### Task 8: Add UI conditional rendering in customer details view

**Files:**
- Modify: `resources/js/data/customer/customer-details-data.js` or `resources/js/data/customer/enhanced-customer-data.js` (whichever manages action buttons)
- Modify: `resources/views/pages/customer/customer-details.blade.php`

**Step 1: Store allowed_actions from API response in Alpine.js state**

In the JavaScript file that fetches customer details, store the `allowed_actions` array when the API response comes back:

```javascript
this.allowedActions = data.allowed_actions || [];
this.customerStatus = data.account_status?.status || 'UNKNOWN';
```

**Step 2: Add Alpine.js conditional rendering to action buttons**

In the Blade view, wrap action buttons with `x-show` directives:

For edit button:
```html
<button x-show="allowedActions.includes('edit')" @click="openEditModal()">
    <i class="fas fa-edit mr-2"></i>Edit
</button>
```

For delete button:
```html
<button x-show="allowedActions.includes('delete')" @click="confirmDelete()">
    <i class="fas fa-trash mr-2"></i>Delete
</button>
```

For "New Application" button:
```html
<button x-show="allowedActions.includes('create_application')" @click="goToApplication()">
    <i class="fas fa-plus mr-2"></i>New Application
</button>
```

For approve/decline (only visible for PENDING):
```html
<div x-show="allowedActions.includes('approve')">
    <button @click="approveCustomer()">Approve</button>
    <button @click="declineCustomer()">Decline</button>
</div>
```

For reactivate (only visible for INACTIVE):
```html
<button x-show="allowedActions.includes('reactivate')" @click="reactivateCustomer()">
    <i class="fas fa-redo mr-2"></i>Reactivate
</button>
```

**Step 3: Add a status banner for PENDING and INACTIVE customers**

At the top of the customer details page (right after the page header), add conditional banners:

```html
<div x-show="customerStatus === 'PENDING'" class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
    <div class="flex items-center">
        <i class="fas fa-clock text-yellow-500 mr-3"></i>
        <div>
            <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-300">Pending Approval</h4>
            <p class="text-sm text-yellow-700 dark:text-yellow-400">This customer is awaiting approval. Only viewing is available until approved.</p>
        </div>
    </div>
</div>

<div x-show="customerStatus === 'INACTIVE'" class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
    <div class="flex items-center">
        <i class="fas fa-ban text-red-500 mr-3"></i>
        <div>
            <h4 class="text-sm font-semibold text-red-800 dark:text-red-300">Inactive Customer</h4>
            <p class="text-sm text-red-700 dark:text-red-400">This customer is inactive. Only viewing and paying existing bills is available.</p>
        </div>
    </div>
</div>
```

**Step 4: Test manually in browser**

1. Navigate to a PENDING customer's details page — verify only "Approve/Decline" buttons visible
2. Navigate to an ACTIVE customer's details page — verify all action buttons visible
3. Navigate to an INACTIVE customer's details page — verify only "Reactivate" and payment buttons visible

**Step 5: Commit**

```bash
git add resources/views/pages/customer/customer-details.blade.php resources/js/data/customer/
git commit -m "feat(customer): conditionally render action buttons based on customer status"
```

---

### Task 9: Create CheckCustomerStatus middleware

**Files:**
- Create: `app/Http/Middleware/CheckCustomerStatus.php`
- Modify: `bootstrap/app.php` (register middleware alias)
- Modify: `routes/web.php` (apply middleware to customer-action routes)

**Step 1: Create the middleware**

```php
<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Services\Customers\CustomerStatusService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCustomerStatus
{
    public function __construct(
        protected CustomerStatusService $customerStatusService
    ) {}

    /**
     * Validate customer status before allowing the action.
     *
     * Usage: Route::middleware('customer.status:create_application')
     *
     * @param  string  $action  The action being attempted (create_application, process_payment, edit, delete)
     */
    public function handle(Request $request, Closure $next, string $action): Response
    {
        $customerId = $request->route('id')
            ?? $request->input('customer_id')
            ?? $request->route('customerId');

        if (! $customerId) {
            return $next($request);
        }

        $customer = Customer::with('status')->find($customerId);

        if (! $customer) {
            return $next($request);
        }

        $allowedActions = $this->customerStatusService->getCustomerAllowedActions($customer);

        if (! in_array($action, $allowedActions)) {
            $status = $this->customerStatusService->getCustomerStatusDescription($customer);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "This action is not allowed for customers with {$status} status.",
                ], 403);
            }

            abort(403, "This action is not allowed for customers with {$status} status.");
        }

        return $next($request);
    }
}
```

**Step 2: Register the middleware alias**

In `bootstrap/app.php`, find the middleware configuration and add the alias. Look for existing alias registrations (like `permission`, `role`) and add:

```php
'customer.status' => \App\Http\Middleware\CheckCustomerStatus::class,
```

**Step 3: Apply middleware to relevant routes**

In `routes/web.php`, add the middleware to customer-mutation routes. Find the customer manage group (around line 134) and wrap specific routes:

```php
Route::put('/customer/{id}', [CustomerController::class, 'update'])
    ->middleware('customer.status:edit')
    ->name('customer.update');

Route::delete('/customer/{id}', [CustomerController::class, 'destroy'])
    ->middleware('customer.status:delete')
    ->name('customer.destroy');
```

**Step 4: Run full test suite**

Run: `php artisan test`
Expected: ALL PASS

**Step 5: Commit**

```bash
git add app/Http/Middleware/CheckCustomerStatus.php bootstrap/app.php routes/web.php
git commit -m "feat(middleware): add CheckCustomerStatus middleware for route-level protection"
```

---

### Task 10: Add status validation to customer list view actions

**Files:**
- Modify: `app/Services/Customers/CustomerService.php` (add allowed_actions to list response)
- Modify: `resources/views/pages/customer/customer-list.blade.php` (conditionally show row actions)

**Step 1: Add allowed_actions to the customer list API response**

In `app/Services/Customers/CustomerService.php`, find the `getCustomerList()` method. In the section where customer rows are mapped (around line 100-115), add:

```php
'allowed_actions' => $this->customerStatusService->getCustomerAllowedActions($customer),
```

**Step 2: In the customer list Blade/JS, conditionally render row actions**

In the DataTable row rendering, use the `allowed_actions` array to show/hide per-row action buttons:

- "View" button: always shown
- "Edit" button: only if `allowed_actions.includes('edit')`
- "Delete" button: only if `allowed_actions.includes('delete')`

**Step 3: Test manually in browser**

Verify that:
- ACTIVE customers show all action buttons in their row
- PENDING customers show only "View"
- INACTIVE customers show "View" (and "Reactivate" if added)

**Step 4: Commit**

```bash
git add app/Services/Customers/CustomerService.php resources/views/pages/customer/customer-list.blade.php
git commit -m "feat(customer-list): conditionally show actions based on customer status"
```

---

### Task 11: Final integration test and cleanup

**Files:**
- Test: `tests/Feature/Services/Customer/CustomerStatusIntegrationTest.php`

**Step 1: Write an integration test that validates the full flow**

```php
<?php

namespace Tests\Feature\Services\Customer;

use App\Models\Customer;
use App\Models\Status;
use App\Services\Customers\CustomerApprovalService;
use App\Services\Customers\CustomerStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Status::create(['stat_id' => 1, 'stat_desc' => 'ACTIVE']);
    Status::create(['stat_id' => 2, 'stat_desc' => 'INACTIVE']);
    Status::create(['stat_id' => 3, 'stat_desc' => 'PENDING']);

    $this->statusService = app(CustomerStatusService::class);
    $this->approvalService = app(CustomerApprovalService::class);
});

test('full customer lifecycle: PENDING -> ACTIVE -> INACTIVE -> reactivated ACTIVE', function () {
    // 1. Customer starts as PENDING
    $customer = Customer::create([
        'cust_first_name' => 'LIFECYCLE',
        'cust_last_name' => 'TEST',
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-LIFE-001',
    ]);

    $actions = $this->statusService->getCustomerAllowedActions($customer);
    expect($actions)->toContain('approve')->toContain('decline');
    expect($actions)->not->toContain('create_application');

    // 2. Approve -> ACTIVE
    $customer = $this->approvalService->approveCustomer($customer->cust_id);
    expect($this->statusService->getCustomerStatusDescription($customer))->toBe('ACTIVE');

    $actions = $this->statusService->getCustomerAllowedActions($customer);
    expect($actions)->toContain('create_application')->toContain('edit');

    // 3. Manually set INACTIVE (simulating deactivation)
    $customer->update(['stat_id' => Status::getIdByDescription(Status::INACTIVE)]);
    $customer = $customer->fresh('status');

    $actions = $this->statusService->getCustomerAllowedActions($customer);
    expect($actions)->toContain('reactivate')->toContain('process_payment');
    expect($actions)->not->toContain('create_application');

    // 4. Reactivate -> ACTIVE again
    $customer = $this->approvalService->reactivateCustomer($customer->cust_id);
    expect($this->statusService->getCustomerStatusDescription($customer))->toBe('ACTIVE');

    $actions = $this->statusService->getCustomerAllowedActions($customer);
    expect($actions)->toContain('create_application');
});

test('PENDING customer is blocked from all mutations', function () {
    $customer = Customer::create([
        'cust_first_name' => 'BLOCKED',
        'cust_last_name' => 'PENDING',
        'stat_id' => Status::getIdByDescription(Status::PENDING),
        'c_type' => 'RESIDENTIAL',
        'create_date' => now(),
        'resolution_no' => 'INITAO-BLOCK-001',
    ]);

    expect(fn () => $this->statusService->assertCustomerCanCreateApplication($customer))
        ->toThrow(\Exception::class);

    expect(fn () => $this->statusService->assertCustomerCanProcessPayment($customer))
        ->toThrow(\Exception::class);

    expect(fn () => $this->statusService->assertCustomerCanEdit($customer))
        ->toThrow(\Exception::class);

    expect(fn () => $this->statusService->assertCustomerCanDelete($customer))
        ->toThrow(\Exception::class);
});
```

**Step 2: Run all tests**

Run: `php artisan test`
Expected: ALL PASS

**Step 3: Run code formatter**

Run: `./vendor/bin/pint`

**Step 4: Final commit**

```bash
git add tests/Feature/Services/Customer/CustomerStatusIntegrationTest.php
git add -u  # stage any pint formatting changes
git commit -m "test(customer): add integration tests for full customer status lifecycle"
```

---

## File Impact Summary

| File | Action | Task |
|------|--------|------|
| `app/Services/Customers/CustomerStatusService.php` | CREATE | 1 |
| `app/Services/Customers/CustomerApprovalService.php` | CREATE | 2 |
| `app/Http/Controllers/Customer/CustomerApprovalController.php` | REWRITE | 2 |
| `app/Services/ServiceApplication/ServiceApplicationService.php` | MODIFY (inject + add check) | 4 |
| `app/Services/Payment/PaymentService.php` | MODIFY (inject + add checks x3) | 5 |
| `app/Services/Customers/CustomerService.php` | MODIFY (inject + add checks x3 + tighten search) | 6, 7, 10 |
| `app/Http/Middleware/CheckCustomerStatus.php` | CREATE | 9 |
| `bootstrap/app.php` | MODIFY (register alias) | 9 |
| `routes/web.php` | MODIFY (add reactivate route + middleware) | 3, 9 |
| `resources/views/pages/customer/customer-details.blade.php` | MODIFY (add banners + conditional buttons) | 8 |
| `resources/js/data/customer/*.js` | MODIFY (store allowedActions) | 8 |
| `resources/views/pages/customer/customer-list.blade.php` | MODIFY (conditional row actions) | 10 |
| `tests/Feature/Services/Customer/CustomerStatusServiceTest.php` | CREATE | 1 |
| `tests/Feature/Services/Customer/CustomerApprovalServiceTest.php` | CREATE | 2 |
| `tests/Feature/Services/Customer/ServiceApplicationStatusCheckTest.php` | CREATE | 4 |
| `tests/Feature/Services/Customer/CustomerStatusIntegrationTest.php` | CREATE | 11 |
