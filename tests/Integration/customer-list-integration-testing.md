# Customer List Integration Testing Guide

## Overview

This guide provides comprehensive integration testing procedures for the customer list redesign project. Integration tests verify that all components (backend API, frontend JavaScript, UI components) work together correctly.

## Test Environment Setup

### Prerequisites

```bash
# 1. Ensure database is migrated and seeded
php artisan migrate:fresh --seed

# 2. Seed test customers (at least 100)
php artisan db:seed --class=CustomerSeeder

# 3. Start the development server
php artisan serve

# 4. Start Vite dev server (for frontend assets)
npm run dev
```

### Generate Test Data

To create 100+ customers for performance testing:

```bash
# Use tinker to generate customers
php artisan tinker

# Then run:
\App\Models\Customer::factory()->count(150)->create();
exit
```

Or use the provided seeder command (see Performance Testing section).

---

## Test Scenarios

### 1. Complete Flow Test: Page Load → Stats Load → Table Load

**Objective:** Verify the entire page loads correctly with all components working together.

**Steps:**

1. **Open Browser**
   - Navigate to: `http://localhost:8000/customer/list`
   - You must be logged in with `customers.view` permission

2. **Verify Initial Page Load**
   - Page title should be "Customer List"
   - Layout should render without errors
   - Navigation menu should be active on "Customers"

3. **Verify Stats Cards Load**
   - Four stat cards should appear at the top:
     - Total Customers
     - Residential Customers
     - Total Outstanding Bills
     - Overdue Accounts
   - Each card should show a number (not "..." or "Loading")
   - Cards should load within 1 second

4. **Verify Table Loads**
   - Customer table should appear below stats
   - Table headers should be visible: Name, Resolution No., Location, Type, Meter No., Current Bill, Status, Actions
   - Rows should populate with customer data
   - Table should load within 2 seconds (even with 100+ records)
   - Pagination controls should appear at the bottom

5. **Check Browser Console**
   - Open Developer Tools (F12)
   - Console tab should have no errors
   - Network tab should show successful API calls:
     - `GET /customer/stats` → 200 OK
     - `GET /customer/list?page=1&per_page=10` → 200 OK

**Expected Results:**
- All components load successfully
- No JavaScript errors in console
- All API calls return 200 status
- Stats and table data are visible and formatted correctly

**Automated Test:** See `CustomerListIntegrationTest::test_complete_page_load_flow()`

---

### 2. Search Workflow Test

**Objective:** Verify search functionality updates the table and preserves state across pagination.

**Steps:**

1. **Navigate to Customer List**
   - URL: `http://localhost:8000/customer/list`

2. **Perform Search**
   - Type a customer name in the search box (e.g., "JOHN")
   - Wait for debounce (300ms)
   - Observe table updates

3. **Verify Search Results**
   - Table should filter to show only matching customers
   - Result count should update (e.g., "Showing 1 to 5 of 8 entries")
   - All visible rows should contain the search term

4. **Test Pagination with Search**
   - If search results span multiple pages, navigate to page 2
   - URL should include: `?search=JOHN&page=2`
   - Search filter should persist
   - Only matching results should appear

5. **Clear Search**
   - Clear the search box
   - Table should return to showing all customers
   - Pagination should reset to page 1

6. **Search Edge Cases**
   - Search for non-existent name: "ZZZNONEXISTENT"
     - Table should show "No customers found"
   - Search by resolution number: "INITAO-"
     - Should find customers with matching resolution numbers
   - Search with special characters: "O'BRIEN"
     - Should handle apostrophes correctly

**Expected Results:**
- Search filters table immediately after debounce
- Pagination preserves search parameter
- URL updates to reflect search state
- Empty results show appropriate message
- Clearing search restores full list

**Network Verification:**
- Check Network tab for: `GET /customer/list?search=JOHN&page=1&per_page=10`
- Response should include filtered `data` array
- `total` should reflect filtered count

**Automated Test:** See `CustomerListIntegrationTest::test_search_workflow_with_pagination()`

---

### 3. Filter Workflow Test

**Objective:** Verify status filter updates the table and preserves state across pagination.

**Steps:**

1. **Navigate to Customer List**
   - URL: `http://localhost:8000/customer/list`

2. **Apply Status Filter**
   - Click on status dropdown (if implemented)
   - Select "ACTIVE" status
   - Observe table updates

3. **Verify Filter Results**
   - Table should show only ACTIVE customers
   - All visible status badges should be green (ACTIVE)
   - Result count should update

4. **Test Pagination with Filter**
   - Navigate to page 2
   - URL should include: `?status_filter=ACTIVE&page=2`
   - Filter should persist
   - Only ACTIVE customers should appear

5. **Change Filter**
   - Switch to "PENDING" status
   - Table should update to show only PENDING customers
   - Pagination should reset to page 1

6. **Clear Filter**
   - Select "All" or clear filter
   - Table should show all customers regardless of status

**Expected Results:**
- Filter updates table immediately
- Pagination preserves filter parameter
- URL updates to reflect filter state
- Multiple status badges should be visible across different filters

**Network Verification:**
- Check Network tab for: `GET /customer/list?status_filter=ACTIVE&page=1&per_page=10`
- Response `data` should only include customers with matching status

**Automated Test:** See `CustomerListIntegrationTest::test_filter_workflow_with_pagination()`

---

### 4. Combined Search + Filter Test

**Objective:** Verify search and filter work together correctly.

**Steps:**

1. **Navigate to Customer List**

2. **Apply Both Search and Filter**
   - Enter search term: "SMITH"
   - Select status filter: "ACTIVE"
   - Observe table updates

3. **Verify Combined Results**
   - Table should show only ACTIVE customers named SMITH
   - Result count should reflect both filters
   - URL should include both parameters: `?search=SMITH&status_filter=ACTIVE`

4. **Test Pagination**
   - Navigate through pages
   - Both filters should persist

5. **Modify One Filter**
   - Change search to "JONES" (keep ACTIVE filter)
   - Table should update to show ACTIVE customers named JONES
   - Should reset to page 1

6. **Clear One Filter**
   - Clear search (keep status filter)
   - Should show all ACTIVE customers

**Expected Results:**
- Both filters apply simultaneously
- Pagination preserves both parameters
- Modifying one filter keeps the other active
- URL always reflects current filter state

**Network Verification:**
- Check Network tab for: `GET /customer/list?search=SMITH&status_filter=ACTIVE&page=1&per_page=10`

**Automated Test:** See `CustomerListIntegrationTest::test_combined_search_and_filter()`

---

### 5. Pagination Workflow Test

**Objective:** Verify pagination controls work correctly and preserve state.

**Steps:**

1. **Navigate to Customer List**
   - Ensure database has 20+ customers

2. **Test Page Size Change**
   - Default should be 10 items per page
   - Change to 25 items per page (if dropdown exists)
   - Table should update to show 25 rows
   - URL should update: `?per_page=25`
   - Pagination controls should update accordingly

3. **Test Page Navigation**
   - Click "Next" button
   - Should navigate to page 2
   - URL should update: `?page=2&per_page=25`
   - Different customers should appear

4. **Test Direct Page Navigation**
   - Click page number "3" directly
   - Should navigate to page 3
   - URL should update: `?page=3&per_page=25`

5. **Test First/Last Page**
   - Click "Last" button (if exists)
   - Should jump to final page
   - "Next" button should be disabled
   - Click "First" button
   - Should jump to page 1
   - "Previous" button should be disabled

6. **Pagination with Filters**
   - Apply search: "JOHN"
   - Navigate to page 2 of search results
   - URL should be: `?search=JOHN&page=2&per_page=25`
   - Search should persist across pages

**Expected Results:**
- Page size controls update displayed rows
- Page navigation updates URL and content
- Disabled states appear correctly on first/last pages
- Pagination preserves all filter parameters

**Network Verification:**
- Check Network tab for correct pagination parameters
- Verify `current_page`, `last_page`, `per_page`, `total` in response

**Automated Test:** See `CustomerListIntegrationTest::test_pagination_workflow()`

---

### 6. Export Functionality Test (If Implemented)

**Objective:** Verify export functionality exports filtered data correctly.

**Steps:**

1. **Navigate to Customer List**

2. **Test Export All**
   - Click "Export" button (if exists)
   - Should download CSV/Excel file
   - File should contain all customers

3. **Test Export Filtered Data**
   - Apply search: "SMITH"
   - Click "Export" button
   - Downloaded file should only contain customers matching "SMITH"

4. **Verify Export Content**
   - Open downloaded file
   - Should include headers: Name, Resolution No., Location, Type, Meter No., Current Bill, Status
   - Data should match table display

**Expected Results:**
- Export respects current filters
- File format is correct (CSV/Excel)
- All columns are included
- Data is properly formatted

**Note:** This test is optional if export is not yet implemented.

---

### 7. Performance Test: 100+ Customer Records

**Objective:** Verify page performance with large datasets.

**Setup:**

```bash
# Create performance test seeder
php artisan make:seeder PerformanceTestCustomerSeeder
```

Then add this code to the seeder:

```php
<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class PerformanceTestCustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Create 150 customers for performance testing
        Customer::factory()->count(150)->create();

        $this->command->info('Created 150 customers for performance testing');
    }
}
```

Run the seeder:

```bash
php artisan db:seed --class=PerformanceTestCustomerSeeder
```

**Test Steps:**

1. **Clear Browser Cache**
   - Open DevTools → Network tab
   - Check "Disable cache"
   - Clear browser cache (Ctrl+Shift+Delete)

2. **Measure Initial Page Load**
   - Navigate to: `http://localhost:8000/customer/list`
   - Open Network tab → Performance tab
   - Record page load time
   - **Requirement:** Must load in < 2 seconds

3. **Measure Stats API Call**
   - Check Network tab for `GET /customer/stats`
   - Response time should be < 500ms

4. **Measure Table API Call**
   - Check Network tab for `GET /customer/list?page=1&per_page=10`
   - Response time should be < 1 second
   - Even with 150+ customers in database

5. **Test Table Rendering**
   - Measure time from API response to table display
   - Should render within 300ms

6. **Test Search Performance**
   - Search for common name (e.g., "JOHN")
   - API call should complete in < 1 second
   - Table update should be immediate

7. **Test Pagination Performance**
   - Navigate through pages
   - Each page change should complete in < 500ms

**Performance Benchmarks:**

| Metric | Target | Maximum |
|--------|--------|---------|
| Page Load (DOMContentLoaded) | < 1.5s | < 2s |
| Stats API Response | < 300ms | < 500ms |
| Table API Response | < 500ms | < 1s |
| Table Rendering | < 200ms | < 300ms |
| Search API Response | < 500ms | < 1s |
| Pagination Response | < 300ms | < 500ms |

**Expected Results:**
- All metrics should be within "Target" range
- No metric should exceed "Maximum" threshold
- Page should remain responsive during all operations
- No memory leaks after multiple operations

**Optimization Notes:**
- If performance is poor, check:
  - Database indexes on searchable columns
  - Eager loading relationships (avoid N+1 queries)
  - Response caching for stats endpoint
  - Frontend debouncing for search

**Automated Test:** See `CustomerListIntegrationTest::test_performance_with_large_dataset()`

---

### 8. Network Test: Verify API Calls

**Objective:** Verify all API calls use correct endpoints, headers, and parameters.

**Steps:**

1. **Open Developer Tools**
   - Press F12
   - Go to Network tab
   - Clear existing requests

2. **Load Customer List Page**
   - Navigate to: `http://localhost:8000/customer/list`
   - Monitor network requests

3. **Verify Stats API Call**
   - Should see: `GET /customer/stats`
   - **Request Headers:**
     - `Accept: application/json`
     - `X-Requested-With: XMLHttpRequest` (if AJAX)
   - **Response:**
     - Status: 200 OK
     - Content-Type: `application/json`
     - Body structure:
       ```json
       {
         "total_customers": 150,
         "residential_count": 120,
         "total_current_bill": "125000.00",
         "overdue_count": 15
       }
       ```

4. **Verify Table API Call**
   - Should see: `GET /customer/list?page=1&per_page=10`
   - **Request Headers:**
     - `Accept: application/json`
     - `X-Requested-With: XMLHttpRequest`
   - **Response:**
     - Status: 200 OK
     - Content-Type: `application/json`
     - Body structure:
       ```json
       {
         "data": [
           {
             "cust_id": 1,
             "customer_name": "JOHN PAUL DOE",
             "cust_first_name": "JOHN",
             "cust_middle_name": "PAUL",
             "cust_last_name": "DOE",
             "contact_number": "09123456789",
             "location": "Purok 1, Barangay Poblacion",
             "land_mark": "MAIN STREET",
             "created_at": "2024-01-15 10:30:00",
             "status": "ACTIVE",
             "status_badge": "<span class='...'>ACTIVE</span>",
             "resolution_no": "INITAO-JDO-1234567890",
             "c_type": "RESIDENTIAL",
             "meter_no": "MTR-001",
             "current_bill": "₱1,250.50"
           }
         ],
         "current_page": 1,
         "last_page": 15,
         "per_page": 10,
         "total": 150,
         "from": 1,
         "to": 10
       }
       ```

5. **Test Search API Call**
   - Enter search: "JOHN"
   - Should see: `GET /customer/list?search=JOHN&page=1&per_page=10`
   - Response should include filtered data only

6. **Test Filter API Call**
   - Select status: "ACTIVE"
   - Should see: `GET /customer/list?status_filter=ACTIVE&page=1&per_page=10`
   - Response should include filtered data only

7. **Test Combined Parameters**
   - Apply search + filter + pagination
   - Should see: `GET /customer/list?search=JOHN&status_filter=ACTIVE&page=2&per_page=10`
   - All parameters should be present

8. **Verify Error Handling**
   - Simulate network error (DevTools → Network → Offline)
   - Page should show error message
   - Retry should work when back online

**Expected Results:**
- All API calls use correct endpoints
- Request headers include `Accept: application/json`
- Response format matches JSON structure
- HTTP status codes are appropriate (200, 401, 403, 500)
- Error responses include error messages
- CSRF tokens are included in POST requests (if applicable)

**Automated Test:** See `CustomerListIntegrationTest::test_api_calls_have_correct_headers()`

---

## Automated Integration Tests

All manual tests above have corresponding automated tests in:

**File:** `tests/Feature/Customer/CustomerListIntegrationTest.php`

### Running Automated Tests

```bash
# Run all integration tests
php artisan test tests/Feature/Customer/CustomerListIntegrationTest.php

# Run specific test
php artisan test --filter=test_complete_page_load_flow

# Run with coverage
php artisan test tests/Feature/Customer/CustomerListIntegrationTest.php --coverage
```

### Test Coverage

The automated tests cover:

1. Complete page load flow (stats + table)
2. Search workflow with pagination
3. Filter workflow with pagination
4. Combined search and filter
5. Pagination state preservation
6. API response structure validation
7. Performance with 100+ records
8. Error handling scenarios
9. Permission checks
10. Authentication requirements

---

## Browser Compatibility Testing

Test the customer list in multiple browsers:

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

**Key Areas to Test:**
- Table rendering
- Search input debouncing
- Pagination controls
- Responsive layout (mobile, tablet, desktop)
- Console errors (should be none)

---

## Accessibility Testing

### Keyboard Navigation

1. Tab through all interactive elements
2. Search box should be focusable
3. Pagination buttons should be keyboard accessible
4. Table rows should be clickable via keyboard

### Screen Reader Testing

1. Use NVDA or JAWS
2. Stats cards should announce values
3. Table should announce headers
4. Pagination should announce current page

### Color Contrast

1. Status badges should have sufficient contrast
2. Text should be readable in all states
3. Focus indicators should be visible

---

## Troubleshooting

### Stats Cards Not Loading

**Symptoms:** Cards show "..." forever

**Check:**
```bash
# Test API directly
curl -H "Accept: application/json" http://localhost:8000/customer/stats

# Expected response: JSON with stats
```

**Solutions:**
- Check browser console for JavaScript errors
- Verify API route exists
- Check user has `customers.view` permission
- Verify CustomerService::getCustomerStats() method exists

### Table Not Loading

**Symptoms:** Empty table or infinite loading

**Check:**
```bash
# Test API directly
curl -H "Accept: application/json" http://localhost:8000/customer/list?page=1&per_page=10

# Expected response: JSON with data array and pagination
```

**Solutions:**
- Check browser console for errors
- Verify database has customer records
- Check CustomerService::getCustomerList() method
- Verify eager loading relationships are correct

### Search Not Working

**Symptoms:** Search doesn't filter results

**Check:**
- Console for JavaScript errors
- Network tab for search parameter in URL
- Backend receives search parameter
- Database query includes WHERE clause for search

**Solutions:**
- Check debounce timer (300ms)
- Verify search input event listener
- Check backend search logic in CustomerService
- Test API directly with search parameter

### Pagination Not Working

**Symptoms:** Page changes but content doesn't update

**Check:**
- URL updates with page parameter
- API called with correct page number
- Response includes correct page data

**Solutions:**
- Verify pagination controls are wired correctly
- Check Alpine.js state management
- Verify backend pagination logic

### Performance Issues

**Symptoms:** Slow page load, laggy search

**Check:**
```bash
# Check for N+1 queries
php artisan debugbar:enable

# Monitor database queries in debugbar
```

**Solutions:**
- Add database indexes on searchable columns
- Use eager loading for relationships
- Implement response caching
- Optimize frontend rendering
- Use pagination to limit results

---

## Success Criteria

Integration testing is successful when:

- All manual test scenarios pass
- All automated tests pass (100% success rate)
- Page loads in < 2 seconds with 100+ customers
- No JavaScript console errors
- All API calls return correct responses
- Search and filter work correctly together
- Pagination preserves filter state
- Performance benchmarks are met
- Cross-browser compatibility confirmed
- Accessibility requirements met

---

## Maintenance

**Update this guide when:**
- New features are added (export, bulk actions, etc.)
- API endpoints change
- Performance benchmarks need adjustment
- New test scenarios are discovered

**Review schedule:** After each major feature addition or before production deployment.

---

**Last Updated:** 2026-01-25
**Version:** 1.0.0
**Author:** Phase 6.9 - Customer List Redesign Integration Testing
