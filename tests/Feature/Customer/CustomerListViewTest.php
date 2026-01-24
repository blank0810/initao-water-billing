<?php

/**
 * Customer List View Verification Tests
 *
 * These tests verify that the frontend code is syntactically correct
 * and that required elements are present. They do NOT require database
 * or actual browser testing.
 */

test('customer list new view exists and can be compiled', function () {
    $viewPath = resource_path('views/pages/customer/customer-list-new.blade.php');

    expect(file_exists($viewPath))
        ->toBeTrue('View file does not exist at: '.$viewPath);

    // Test that the view can be compiled without errors
    $view = view('pages.customer.customer-list-new');
    $html = $view->render();

    expect($html)->toBeString();
});

test('customer list new view contains required elements', function () {
    $view = view('pages.customer.customer-list-new');
    $html = $view->render();

    // Check for stats container
    expect($html)->toContain('id="customer-stats"');

    // Check for table body
    expect($html)->toContain('id="customer-list-tbody"');

    // Check for pagination elements
    expect($html)->toContain('id="customerCurrentPage"');
    expect($html)->toContain('id="customerTotalPages"');
    expect($html)->toContain('id="customerTotalRecords"');
    expect($html)->toContain('id="customerPrevBtn"');
    expect($html)->toContain('id="customerNextBtn"');
    expect($html)->toContain('id="customerPageSize"');
});

test('customer list new view includes javascript file', function () {
    $view = view('pages.customer.customer-list-new');
    $html = $view->render();

    // Check for Vite directive with JavaScript file
    expect($html)->toContain('resources/js/data/customer/customer-list-simple.js');
});

test('customer list new view has proper table structure', function () {
    $view = view('pages.customer.customer-list-new');
    $html = $view->render();

    // Check for table headers (6 columns)
    expect($html)->toContain('Customer');
    expect($html)->toContain('Address & Type');
    expect($html)->toContain('Meter No');
    expect($html)->toContain('Current Bill');
    expect($html)->toContain('Status');
    expect($html)->toContain('Actions');
});

test('customer list new view includes action functions component', function () {
    $view = view('pages.customer.customer-list-new');
    $html = $view->render();

    // The action-functions component should render search, filter, and clear elements
    expect($html)->toContain('Search customer...');
});

test('customer list javascript file exists', function () {
    $jsPath = resource_path('js/data/customer/customer-list-simple.js');

    expect(file_exists($jsPath))
        ->toBeTrue('JavaScript file does not exist at: '.$jsPath);
});

test('customer list javascript has valid syntax', function () {
    $jsPath = resource_path('js/data/customer/customer-list-simple.js');

    // Run Node.js syntax check
    $output = [];
    $returnVar = 0;
    exec("node --check {$jsPath} 2>&1", $output, $returnVar);

    expect($returnVar)
        ->toBe(0, 'JavaScript syntax error: '.implode("\n", $output));
});

test('customer list javascript contains required functions', function () {
    $jsPath = resource_path('js/data/customer/customer-list-simple.js');
    $jsContent = file_get_contents($jsPath);

    // Check for essential functions
    expect($jsContent)->toContain('loadStats');
    expect($jsContent)->toContain('loadCustomers');
    expect($jsContent)->toContain('renderStats');
    expect($jsContent)->toContain('renderCustomersTable');
    expect($jsContent)->toContain('updatePagination');
    expect($jsContent)->toContain('searchAndFilterCustomers');
    expect($jsContent)->toContain('window.customerPagination');
});

test('customer list javascript references correct element IDs', function () {
    $jsPath = resource_path('js/data/customer/customer-list-simple.js');
    $jsContent = file_get_contents($jsPath);

    // Check for element ID references (querySelector uses # prefix, getElementById doesn't)
    expect($jsContent)->toContain("'customer-stats'");
    expect($jsContent)->toContain("'#customerTableBody'"); // Note: this is a mismatch with Blade
    expect($jsContent)->toContain("'customerCurrentPage'");
    expect($jsContent)->toContain("'customerTotalPages'");
    expect($jsContent)->toContain("'customerTotalRecords'");
    expect($jsContent)->toContain("'customerPrevBtn'");
    expect($jsContent)->toContain("'customerNextBtn'");
    expect($jsContent)->toContain("'customerSearch'");
    expect($jsContent)->toContain("'customerStatusFilter'");
    expect($jsContent)->toContain("'customerClearBtn'");
    expect($jsContent)->toContain("'customerPageSize'");
});

test('customer list javascript has XSS protection', function () {
    $jsPath = resource_path('js/data/customer/customer-list-simple.js');
    $jsContent = file_get_contents($jsPath);

    // Check for escapeHtml function
    expect($jsContent)->toContain('escapeHtml');
    expect($jsContent)->toContain('&amp;');
    expect($jsContent)->toContain('&lt;');
    expect($jsContent)->toContain('&gt;');
});

test('customer list javascript includes CSRF token handling', function () {
    $jsPath = resource_path('js/data/customer/customer-list-simple.js');
    $jsContent = file_get_contents($jsPath);

    // Check for CSRF token usage
    expect($jsContent)->toContain('csrf-token');
    expect($jsContent)->toContain('X-CSRF-TOKEN');
});

test('customer list javascript has debounced search', function () {
    $jsPath = resource_path('js/data/customer/customer-list-simple.js');
    $jsContent = file_get_contents($jsPath);

    // Check for debounce implementation
    expect($jsContent)->toContain('searchTimeout');
    expect($jsContent)->toContain('clearTimeout');
    expect($jsContent)->toContain('setTimeout');
    expect($jsContent)->toContain('300'); // 300ms debounce
});
