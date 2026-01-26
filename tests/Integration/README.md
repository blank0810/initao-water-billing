# Integration Tests

This directory contains integration testing guides and documentation for end-to-end testing workflows.

## Overview

Integration tests verify that multiple components work together correctly. Unlike unit tests (which test individual methods) or feature tests (which test individual endpoints), integration tests verify complete workflows from start to finish.

## Available Guides

### Customer List Integration Testing

**File:** `customer-list-integration-testing.md`

**Purpose:** Comprehensive integration testing guide for the customer list redesign project.

**Covers:**
- Complete flow test: page load → stats load → table load
- Search workflow with pagination
- Filter workflow with pagination
- Combined search and filter
- Pagination state preservation
- Performance testing with 100+ records
- Network API verification
- Error handling
- Authentication and authorization

**Usage:**
```bash
# Read the guide
cat tests/Integration/customer-list-integration-testing.md

# Run automated tests
php artisan test tests/Feature/Customer/CustomerListIntegrationTest.php
```

## Manual vs Automated Testing

### Manual Testing

Manual testing is required for:
- Browser-specific behavior (Chrome vs Firefox vs Safari)
- Visual UI verification
- User experience testing
- Accessibility testing (keyboard navigation, screen readers)
- Performance measurement in real browsers
- Responsive design testing (mobile, tablet, desktop)

Follow the guides in this directory for step-by-step manual testing procedures.

### Automated Testing

Automated tests are available for:
- API endpoint testing
- Data validation
- Business logic verification
- Error handling
- Performance benchmarks (basic)
- State management

Run automated tests with:
```bash
php artisan test tests/Feature/Customer/CustomerListIntegrationTest.php
```

## Test Data Setup

### Quick Setup

```bash
# Refresh database and seed
php artisan migrate:fresh --seed
```

### Performance Testing Data

For testing with large datasets (100+ customers):

```bash
# Run performance test seeder
php artisan db:seed --class=PerformanceTestCustomerSeeder

# This creates 150 customers:
# - 105 ACTIVE (70%)
# - 30 INACTIVE (20%)
# - 15 PENDING (10%)
```

### Custom Test Data

Use Tinker for custom test data:

```bash
php artisan tinker

# Create specific customers
\App\Models\Customer::factory()->count(10)->withName('JOHN', 'SMITH')->create();

# Create customers with specific status
\App\Models\Customer::factory()->count(5)->create([
    'stat_id' => \App\Models\Status::getIdByDescription(\App\Models\Status::ACTIVE)
]);

exit
```

## Performance Benchmarks

All integration tests should verify these performance benchmarks:

| Metric | Target | Maximum |
|--------|--------|---------|
| Page Load (DOMContentLoaded) | < 1.5s | < 2s |
| Stats API Response | < 300ms | < 500ms |
| Table API Response | < 500ms | < 1s |
| Search API Response | < 500ms | < 1s |
| Pagination Response | < 300ms | < 500ms |

These benchmarks apply even with 100+ customers in the database.

## Browser Testing Matrix

Test each integration guide in the following browsers:

- Chrome (latest)
- Firefox (latest)
- Safari (latest, macOS only)
- Edge (latest, Windows only)

Focus on:
- Core functionality (search, filter, pagination)
- JavaScript execution
- API calls
- Error handling

## Continuous Integration

### Running Tests in CI/CD

```bash
# In GitHub Actions / GitLab CI
php artisan migrate:fresh --seed
php artisan test tests/Feature/Customer/CustomerListIntegrationTest.php

# Optionally run with coverage
php artisan test tests/Feature/Customer/CustomerListIntegrationTest.php --coverage
```

### Test Environment

Ensure CI environment has:
- PHP 8.2+
- MySQL 8.0+
- Node.js 18+ (for frontend build)
- Composer dependencies installed
- NPM dependencies installed

## Writing New Integration Guides

When creating new integration testing guides:

1. **Create a new .md file** in this directory
2. **Follow the same structure** as `customer-list-integration-testing.md`:
   - Overview
   - Test Environment Setup
   - Test Scenarios (numbered)
   - Automated Tests reference
   - Performance Benchmarks
   - Troubleshooting
   - Success Criteria

3. **Include both manual and automated tests** where appropriate

4. **Document expected results** clearly for each test step

5. **Add troubleshooting section** with common issues and solutions

6. **Update this README** to include the new guide

## Best Practices

1. **Test the happy path first** - Ensure core functionality works
2. **Test edge cases** - Empty results, large datasets, special characters
3. **Test error handling** - Network errors, permission errors, validation errors
4. **Test state preservation** - Filters, search, pagination should persist
5. **Test performance** - Always verify performance benchmarks
6. **Test accessibility** - Keyboard navigation, screen readers
7. **Test cross-browser** - Don't assume Chrome behavior is universal

## Troubleshooting

### Tests Failing

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Refresh database
php artisan migrate:fresh --seed

# Run specific test
php artisan test --filter=test_complete_page_load_flow
```

### Performance Issues

```bash
# Enable query logging
php artisan debugbar:enable

# Check for N+1 queries
# Run the test and check debugbar output
```

### Permission Errors

```bash
# Re-seed permissions
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
```

## Related Documentation

- Feature Tests: `tests/Feature/Customer/`
- Unit Tests: `tests/Unit/Services/Customers/`
- API Documentation: `.claude/FEATURES.md`
- Project Setup: `.claude/SETUP.md`

## Contact

For questions about integration testing:
- Review the testing guides in this directory
- Check existing automated tests in `tests/Feature/`
- Refer to Laravel testing documentation: https://laravel.com/docs/testing

---

**Last Updated:** 2026-01-25
**Maintained by:** Development Team
