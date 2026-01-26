# Customer List Frontend Testing Checklist

## Overview
This document provides a comprehensive testing checklist for the new customer list view (`/customer/list-new`).

**Files Under Test:**
- View: `resources/views/pages/customer/customer-list-new.blade.php`
- JavaScript: `resources/js/data/customer/customer-list-simple.js`
- Backend API: `GET /customer/stats`, `GET /customer/list`

**Testing Environment:**
- Browser: Chrome, Firefox, Safari (latest versions)
- Screen Sizes: Mobile (375px), Tablet (768px), Desktop (1280px+)
- Dark Mode: Both light and dark themes

---

## 1. Stats Cards Section

### 1.1 Initial Load
- [ ] Four skeleton cards appear on page load
- [ ] Skeleton cards have pulsing animation
- [ ] Stats API endpoint `/customer/stats` is called
- [ ] CSRF token is included in request headers

### 1.2 Data Display
- [ ] Stats cards replace skeleton after successful API response
- [ ] Total Customers card shows correct count
- [ ] Active Customers card shows correct count
- [ ] Pending Customers card shows correct count
- [ ] Inactive Customers card shows correct count
- [ ] Numbers are formatted with locale-aware thousands separators
- [ ] Zero values display as "0" not blank

### 1.3 Loading States
- [ ] If stats API fails, skeleton remains visible
- [ ] Console error is logged on API failure
- [ ] Page remains functional if stats fail

### 1.4 Dark Mode
- [ ] All cards have correct dark mode background colors
- [ ] Icons maintain visibility in dark mode
- [ ] Text colors are readable in dark mode

---

## 2. Table Rendering Section

### 2.1 Column Structure
- [ ] Table has exactly 6 columns
- [ ] Column headers: Customer, Address & Type, Meter No, Current Bill, Status, Actions
- [ ] Column headers are uppercase and properly styled
- [ ] Header background is gray-50 (light) / gray-700 (dark)

### 2.2 Customer Column (Column 1)
- [ ] Avatar circle displays with gradient background
- [ ] Initials are extracted correctly from customer name
- [ ] Single name shows first 2 characters as initials
- [ ] Multiple names show first letter + last letter as initials
- [ ] Customer name displays below avatar
- [ ] Customer ID displays below name in gray text
- [ ] "N/A" displays if customer ID is missing

### 2.3 Address & Type Column (Column 2)
- [ ] Location displays on first line
- [ ] Customer type (c_type) displays on second line in gray
- [ ] "N/A" displays for missing values

### 2.4 Meter Number Column (Column 3)
- [ ] Meter number displays in monospace font
- [ ] "N/A" displays if meter number is missing

### 2.5 Current Bill Column (Column 4)
- [ ] Amount displays right-aligned
- [ ] Philippine peso symbol (₱) displays
- [ ] Amount formatted with 2 decimal places
- [ ] Thousands separator displays for large amounts
- [ ] Zero displays as "₱0.00" not blank
- [ ] Missing values display as "₱0.00"

### 2.6 Status Column (Column 5)
- [ ] Status badge displays with correct color:
  - ACTIVE: Green background
  - PENDING: Yellow background
  - INACTIVE: Gray background
  - SUSPENDED: Red background
- [ ] Status text is uppercase
- [ ] Badge has rounded corners
- [ ] Dark mode colors are appropriate

### 2.7 Actions Column (Column 6)
- [ ] "View" link displays in blue
- [ ] Link URL is `/customer/{id}` format
- [ ] Link hover state changes color to darker blue
- [ ] Link is right-aligned

### 2.8 Empty States
- [ ] Empty icon displays when no results
- [ ] "No customers found" message displays
- [ ] Helpful message about adjusting search/filter displays
- [ ] Empty state spans all 6 columns

---

## 3. Pagination Section

### 3.1 Initial State
- [ ] Page size selector defaults to "10"
- [ ] Current page displays as "1"
- [ ] Total pages displays correctly from API
- [ ] Total records displays correctly from API
- [ ] Previous button is disabled on first page
- [ ] Next button is enabled if more pages exist

### 3.2 Next Page Navigation
- [ ] Clicking Next increments current page number
- [ ] New data loads from API with correct page parameter
- [ ] Loading spinner displays during fetch
- [ ] Previous button becomes enabled
- [ ] Next button disables on last page

### 3.3 Previous Page Navigation
- [ ] Clicking Previous decrements current page number
- [ ] New data loads from API
- [ ] Next button becomes enabled (if was disabled)
- [ ] Previous button disables on first page

### 3.4 Page Size Changes
- [ ] Changing page size to 5 reloads data
- [ ] Changing page size to 20 reloads data
- [ ] Changing page size to 50 reloads data
- [ ] Current page resets to 1 when page size changes
- [ ] Total pages recalculates based on new page size
- [ ] API request includes `per_page` parameter

### 3.5 Page Counter Display
- [ ] "Page X of Y" updates on navigation
- [ ] "Showing Z results" updates with correct total
- [ ] Numbers use locale formatting for large totals

---

## 4. Search Section

### 4.1 Search Input
- [ ] Search input field is visible
- [ ] Placeholder text is "Search customer..."
- [ ] Input has proper styling in light mode
- [ ] Input has proper styling in dark mode
- [ ] Clear button (X icon) is visible

### 4.2 Search Functionality
- [ ] Typing triggers search after 300ms debounce
- [ ] Search term is sent in `search` query parameter
- [ ] Results filter to matching customers
- [ ] Current page resets to 1 on new search
- [ ] Search works with partial names
- [ ] Search is case-insensitive (backend dependent)

### 4.3 Debouncing
- [ ] Rapid typing doesn't trigger multiple API calls
- [ ] Only one API call fires 300ms after last keystroke
- [ ] Previous timeout is cleared on new keystroke

### 4.4 Clear Search
- [ ] Clicking clear button empties search input
- [ ] Clearing search reloads all customers
- [ ] Clearing search resets to page 1
- [ ] Filter dropdown is also cleared

---

## 5. Status Filter Section

### 5.1 Filter Dropdown
- [ ] Status filter dropdown is visible
- [ ] Dropdown has options: All Status, Active, Pending, Inactive
- [ ] Dropdown styling matches dark mode
- [ ] Default value is "All Status" (empty)

### 5.2 Filter Functionality
- [ ] Selecting ACTIVE filters to active customers only
- [ ] Selecting PENDING filters to pending customers only
- [ ] Selecting INACTIVE filters to inactive customers only
- [ ] Selecting "All Status" shows all customers
- [ ] Filter value is sent in `status` query parameter
- [ ] Current page resets to 1 when filter changes

### 5.3 Combined Search and Filter
- [ ] Search and filter work together
- [ ] Searching with active filter applied shows filtered results
- [ ] Changing filter maintains search term
- [ ] Clearing resets both search and filter

---

## 6. Loading States Section

### 6.1 Initial Load
- [ ] "Loading..." text displays in table on page load
- [ ] Loading message spans all 6 columns
- [ ] Stats show skeleton cards during load

### 6.2 Data Fetching Spinner
- [ ] Animated spinner displays during API calls
- [ ] Spinner is blue and rotates smoothly
- [ ] "Loading customers..." text displays
- [ ] Spinner spans all 6 columns

### 6.3 Error States
- [ ] Red error icon displays on API failure
- [ ] "Error loading customers" message displays
- [ ] Specific error message shows below main message
- [ ] Error state spans all 6 columns
- [ ] Console logs full error details

### 6.4 Network Issues
- [ ] Appropriate error displays on timeout
- [ ] Appropriate error displays on 500 server error
- [ ] Appropriate error displays on 404 endpoint error
- [ ] Appropriate error displays on network offline

---

## 7. Dark Mode Section

### 7.1 Background Colors
- [ ] Page background is correct in dark mode
- [ ] Stats cards have dark background
- [ ] Table header has dark background
- [ ] Table rows have dark background
- [ ] Pagination controls have dark background

### 7.2 Text Colors
- [ ] All text is readable in dark mode
- [ ] Customer names are white/light gray
- [ ] Secondary text is medium gray
- [ ] Links are light blue in dark mode

### 7.3 Borders and Dividers
- [ ] Table borders are visible in dark mode
- [ ] Row dividers are visible in dark mode
- [ ] Card shadows work in dark mode

### 7.4 Interactive Elements
- [ ] Buttons have correct hover states in dark mode
- [ ] Dropdowns have correct styling in dark mode
- [ ] Input fields have correct styling in dark mode
- [ ] Disabled buttons look disabled in dark mode

---

## 8. Responsive Section

### 8.1 Mobile (375px - 767px)
- [ ] Stats cards stack vertically (1 column)
- [ ] Table is horizontally scrollable
- [ ] All columns remain visible with scroll
- [ ] Pagination controls stack vertically
- [ ] Search and filter inputs are full width
- [ ] Touch interactions work smoothly

### 8.2 Tablet (768px - 1023px)
- [ ] Stats cards display in 2 columns
- [ ] Table is horizontally scrollable or columns shrink
- [ ] Pagination controls may stack or inline
- [ ] Search and filter are properly sized

### 8.3 Desktop (1024px+)
- [ ] Stats cards display in 4 columns
- [ ] Table fits without horizontal scroll
- [ ] All columns have appropriate widths
- [ ] Pagination controls are inline
- [ ] Search and filter are inline

### 8.4 Responsive Utilities
- [ ] Flowbite components resize correctly
- [ ] No horizontal overflow on any screen size
- [ ] Text remains readable at all sizes

---

## 9. View Action Section

### 9.1 View Links
- [ ] Each customer row has a "View" link
- [ ] Link URL follows pattern `/customer/{id}`
- [ ] Link opens in same tab by default
- [ ] Link color is blue (light) / light blue (dark)

### 9.2 Link Behavior
- [ ] Clicking link navigates to customer detail page
- [ ] Customer ID in URL matches row data
- [ ] Browser back button returns to list
- [ ] List state is maintained after back navigation (browser dependent)

### 9.3 Accessibility
- [ ] Links are keyboard accessible (Tab navigation)
- [ ] Enter key activates links
- [ ] Links have proper focus states

---

## 10. Performance Testing

### 10.1 Load Times
- [ ] Initial page load is under 2 seconds
- [ ] Stats API response is under 500ms
- [ ] Customer list API response is under 1 second
- [ ] No cumulative layout shift (CLS) issues

### 10.2 JavaScript Performance
- [ ] No console errors on page load
- [ ] No console warnings
- [ ] Debouncing prevents excessive API calls
- [ ] Memory usage is reasonable (no leaks)

### 10.3 Asset Loading
- [ ] Vite builds JavaScript bundle correctly
- [ ] JavaScript file is minified in production
- [ ] CSS is loaded properly
- [ ] No 404 errors for assets

---

## 11. Accessibility Testing

### 11.1 Keyboard Navigation
- [ ] All interactive elements are keyboard accessible
- [ ] Tab order is logical
- [ ] Focus indicators are visible
- [ ] Escape key works where expected

### 11.2 Screen Readers
- [ ] Table headers have proper scope attributes
- [ ] Loading states announce properly
- [ ] Error states announce properly
- [ ] Status badges have readable text

### 11.3 ARIA Attributes
- [ ] Buttons have aria-labels where needed
- [ ] Loading spinners have aria-live regions
- [ ] Disabled states use aria-disabled

---

## 12. XSS Protection Testing

### 12.1 HTML Escaping
- [ ] Customer names with special characters display correctly
- [ ] HTML in customer data doesn't render
- [ ] Script tags in data don't execute
- [ ] SQL injection attempts in search display safely

### 12.2 Sanitization
- [ ] `escapeHtml()` function works correctly
- [ ] All user-generated content is escaped
- [ ] No innerHTML usage with unsanitized data

---

## 13. API Integration Testing

### 13.1 Stats Endpoint
- [ ] `GET /customer/stats` returns correct structure
- [ ] Response includes: total, active, pending, inactive
- [ ] Unauthorized access is handled
- [ ] CSRF token validation works

### 13.2 List Endpoint
- [ ] `GET /customer/list` returns correct structure
- [ ] Response includes: data, current_page, last_page, total
- [ ] Pagination parameters work correctly
- [ ] Search parameter filters results
- [ ] Status parameter filters results

### 13.3 Error Handling
- [ ] 401 Unauthorized redirects to login
- [ ] 403 Forbidden shows appropriate error
- [ ] 404 Not Found shows appropriate error
- [ ] 500 Server Error shows appropriate error

---

## Element ID Verification

### JavaScript References
The following element IDs are used in `customer-list-simple.js`:

- `#customer-stats` - Stats container
- `#customerTableBody` - Table tbody (NOTE: Mismatch - see below)
- `#customerCurrentPage` - Current page number display
- `#customerTotalPages` - Total pages display
- `#customerTotalRecords` - Total records display
- `#customerPrevBtn` - Previous button
- `#customerNextBtn` - Next button
- `#customerSearch` - Search input
- `#customerStatusFilter` - Status filter dropdown
- `#customerClearBtn` - Clear filters button
- `#customerPageSize` - Page size selector

### Blade Template IDs
The following element IDs are in `customer-list-new.blade.php`:

- `#customer-stats` ✓
- `#customer-list-tbody` ⚠️ MISMATCH - JavaScript expects `customerTableBody`
- `#customerCurrentPage` ✓
- `#customerTotalPages` ✓
- `#customerTotalRecords` ✓
- `#customerPrevBtn` ✓
- `#customerNextBtn` ✓
- `#customerPageSize` ✓

### Action Functions Component IDs
The `x-ui.action-functions` component generates IDs based on the `tableId` prop. Since the Blade uses `tableId="customer-list-tbody"`, the generated IDs are:

- `#customer-list-tbody_search` ⚠️ MISMATCH - JavaScript expects `customerSearch`
- `#customer-list-tbody_filter` ⚠️ MISMATCH - JavaScript expects `customerStatusFilter`
- `#customer-list-tbody_clearBtn` ⚠️ MISMATCH - JavaScript expects `customerClearBtn`

---

## Issues Found During Verification

### 1. Table Body Element ID Mismatch ⚠️
**Issue:** JavaScript looks for `#customerTableBody` but Blade has `#customer-list-tbody`

**Impact:** CRITICAL - Table rendering will fail completely

**Fix Required:** Change Blade template `id="customer-list-tbody"` to `id="customerTableBody"`

### 2. Action Functions Component ID Mismatches ⚠️
**Issue:** The `x-ui.action-functions` component uses `tableId` prop to generate dynamic IDs, but JavaScript expects different IDs.

**Current:** Component generates IDs like `{tableId}_search`, `{tableId}_filter`, `{tableId}_clearBtn`
**Expected by JS:** `customerSearch`, `customerStatusFilter`, `customerClearBtn`

**Impact:** CRITICAL - Search, filter, and clear functions will not work

**Fix Options:**
1. Change Blade template to NOT use `x-ui.action-functions` component, create custom elements with correct IDs
2. Update JavaScript to use the component's ID pattern: `customer-list-tbody_search`, etc.
3. Modify component to accept custom IDs instead of generating them

**Recommended Fix:** Option 1 - Replace component with custom HTML elements that match JavaScript expectations

---

## Pre-Launch Checklist

Before deploying to production:

- [ ] Fix Element ID mismatch (customerTableBody vs customer-list-tbody)
- [ ] Verify action-functions component IDs match JavaScript expectations
- [ ] Run JavaScript through linter (no errors)
- [ ] Test all features in Chrome, Firefox, Safari
- [ ] Test all responsive breakpoints
- [ ] Test dark mode on all features
- [ ] Verify API endpoints are accessible
- [ ] Check CSRF token is properly set in layout
- [ ] Load test with 1000+ customer records
- [ ] Test with slow network (3G throttling)
- [ ] Verify no console errors in production build

---

## Testing Notes

**Manual Testing Required:**
This checklist is designed for manual testing by a QA tester or developer with access to a running instance of the application. Automated tests can be added for critical paths, but many UI/UX elements require human verification.

**Browser DevTools:**
Use browser developer tools to:
- Monitor Network tab for API calls
- Check Console for errors
- Inspect Elements for correct rendering
- Simulate slow network with throttling
- Toggle device emulation for responsive testing
- Toggle dark mode in system settings

**Test Data Requirements:**
- Database should have customers with various statuses
- Include customers with missing data (no meter, no bill, etc.)
- Include customers with long names for UI testing
- Include customers with special characters in names

---

Last Updated: 2025-01-24
