# Customer Suffix Field Design

**Date:** 2026-02-21
**Branch:** application-new-field
**Status:** Approved

---

## Overview

Add an optional suffix field (Jr., Sr., II, III, IV, V) to the Customer table and surface it across the service application form, customer CRUD, and all name display locations.

## Suffix Options

Dropdown with predefined options plus an "Other" free-text fallback:

- Jr.
- Sr.
- II
- III
- IV
- V
- Other (reveals free-text input)

## Name Format

Suffix appears after last name, no comma: `"JUAN DELA CRUZ JR."`

---

## Changes

### 1. Database & Model

**Migration:** Add nullable `cust_suffix` column (string, max 10) to `customer` table after `cust_last_name`.

**Customer Model (`app/Models/Customer.php`):**
- Add `cust_suffix` to `$fillable`
- Update `getFullNameAttribute()` to append suffix when present

**Validation:** `nullable|string|max:10` in all validation rules.

### 2. Service Application Form

**View (`resources/views/pages/application/service-application.blade.php`):**
- Add suffix field in Step 2 (New Customer Registration)
- Change name row from 3-column to 4-column grid
- Suffix renders as `<select>` with predefined options + "Other" revealing a free-text input
- Update `customerForm` Alpine.js data to include `suffix`
- Update `customerDisplayName` getter to append suffix

**ServiceApplicationService (`app/Services/ServiceApplication/ServiceApplicationService.php`):**
- `transformCustomerData()` — map `suffix` to `cust_suffix`

### 3. Customer CRUD

**Add Customer form (`resources/views/pages/customer/add-customer.blade.php`):**
- Change name row from 3-column to 4-column grid
- Add suffix dropdown with same options + "Other" fallback
- Update review/summary step to display suffix after last name

**Edit Customer modal (`resources/views/components/ui/customer/modals/edit-customer.blade.php`):**
- Add a suffix dropdown field
- Pre-populate with the customer's existing suffix value

**CustomerController (`app/Http/Controllers/Customer/CustomerController.php`):**
- Add `'cust_suffix' => ['nullable', 'string', 'max:10']` to create and update validation

**CustomerService (`app/Services/Customers/CustomerService.php`):**
- Update inline name formatters in `getCustomerList()`, `searchCustomers()`, `buildCustomerInfo()`, `getLedgerStatementData()` to append suffix

### 4. Print Views & Display

**Service Application Print (`resources/views/pages/application/service-application-print.blade.php`):**
- Append `cust_suffix` to name: `"First M. Last Jr."`

**Service Contract Print (`resources/views/pages/application/service-contract-print.blade.php`):**
- Append `cust_suffix` to uppercase name: `"FIRST MIDDLE LAST JR."`

**CustomerSearchService (`app/Services/Customers/CustomerSearchService.php`):**
- Update `search()` method to include suffix in composed name

### 5. Not Changed

- `CustomerHelper::generateCustomerResolutionNumber()` — uses initials only, suffix not relevant
- `ServiceApplicationController::store()` — passes array through, no direct name handling
