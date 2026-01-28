# Integration Testing Quick Start

## Setup (5 minutes)

```bash
# 1. Refresh database
php artisan migrate:fresh --seed

# 2. Create performance test data
php artisan db:seed --class=PerformanceTestCustomerSeeder

# 3. Start dev server
php artisan serve

# 4. Start Vite (in new terminal)
npm run dev
```

## Run Automated Tests (2 minutes)

```bash
# All integration tests
php artisan test tests/Feature/Customer/CustomerListIntegrationTest.php

# Specific test
php artisan test --filter=test_complete_page_load_flow

# With coverage
php artisan test tests/Feature/Customer/CustomerListIntegrationTest.php --coverage
```

## Manual Browser Testing (10 minutes)

### 1. Basic Flow Test

```
1. Open http://localhost:8000/customer/list
2. Wait for page to load
3. Check stats cards (should show numbers, not "...")
4. Check table has data
5. No console errors (F12)
```

**Expected:** Page loads in < 2 seconds

### 2. Search Test

```
1. Type "JOHN" in search box
2. Wait 300ms (debounce)
3. Table filters to show only "JOHN"
4. Click page 2 if available
5. URL should include ?search=JOHN&page=2
```

**Expected:** Search persists across pagination

### 3. Filter Test

```
1. Select "ACTIVE" from status filter
2. Table shows only ACTIVE customers
3. Click page 2 if available
4. URL should include ?status_filter=ACTIVE&page=2
```

**Expected:** Filter persists across pagination

### 4. Combined Test

```
1. Search for "SMITH"
2. Filter by "ACTIVE"
3. Table shows only ACTIVE customers named SMITH
4. Navigate pages - both filters persist
```

**Expected:** Both filters work together

### 5. Performance Test

```
1. Open DevTools (F12) → Network tab
2. Reload page (Ctrl+R)
3. Check:
   - GET /customer/stats → < 500ms
   - GET /customer/list → < 1s
4. Check Performance tab:
   - DOMContentLoaded → < 2s
```

**Expected:** All metrics within limits

## Test Data

### Check Current Data

```bash
# Count customers
php artisan tinker
>>> \App\Models\Customer::count()
>>> exit
```

### Add More Test Data

```bash
php artisan tinker
>>> \App\Models\Customer::factory()->count(10)->withName('TEST', 'USER')->create();
>>> exit
```

### Reset Data

```bash
php artisan migrate:fresh --seed
php artisan db:seed --class=PerformanceTestCustomerSeeder
```

## Common Issues

### No data in table

```bash
# Check if customers exist
php artisan tinker
>>> \App\Models\Customer::count()

# If 0, seed data
php artisan db:seed --class=PerformanceTestCustomerSeeder
```

### Stats cards show "..."

```bash
# Check browser console for errors
# Test API directly:
curl -H "Accept: application/json" http://localhost:8000/customer/stats

# Should return JSON with stats
```

### Permission errors

```bash
# Check you're logged in at http://localhost:8000/login
# If using dev-login (local only):
curl http://localhost:8000/dev-login
# Then navigate to customer list
```

### Tests failing

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Refresh database
php artisan migrate:fresh --seed

# Run single test
php artisan test --filter=test_complete_page_load_flow
```

## Success Checklist

- [ ] All 15 automated tests pass
- [ ] Page loads in < 2 seconds (even with 150 customers)
- [ ] Search works and persists across pages
- [ ] Filter works and persists across pages
- [ ] Search + Filter work together
- [ ] Pagination preserves all state
- [ ] No console errors in browser
- [ ] Stats cards show actual numbers
- [ ] Table displays customer data correctly
- [ ] API calls return 200 OK

## Next Steps

1. **For detailed testing:** Read `customer-list-integration-testing.md`
2. **For CI/CD setup:** See `README.md` in this directory
3. **For debugging:** Check Laravel logs in `storage/logs/laravel.log`

---

**Time Required:**
- Setup: 5 minutes
- Automated tests: 2 minutes
- Manual testing: 10 minutes
- **Total: ~17 minutes**
