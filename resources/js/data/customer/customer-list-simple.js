/**
 * Customer List - Simple Implementation
 *
 * Handles fetching customer data from the backend API and dynamically rendering
 * the table, stats cards, and pagination/filtering controls.
 */

(function() {
    'use strict';

    // State management
    let currentPage = 1;
    let pageSize = 10;
    let searchTerm = '';
    let filterValue = '';
    let totalPages = 1;
    let totalRecords = 0;
    let searchTimeout = null;

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    /**
     * Fetch and display customer statistics
     */
    async function loadStats() {
        try {
            const response = await fetch('/customer/stats', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const stats = await response.json();
            renderStats(stats);
        } catch (error) {
            console.error('Error loading stats:', error);
            // Keep skeleton if stats fail to load
        }
    }

    /**
     * Render stats cards with defensive programming
     */
    function renderStats(stats) {
        // Validate stats object
        if (!stats || typeof stats !== 'object') {
            console.error('Invalid stats data received:', stats);
            showStatsError();
            return;
        }

        // Update Total Customers stat
        updateStatCard('#stat-total', stats.total_customers, 'number');

        // Update Residential Type stat
        updateStatCard('#stat-residential', stats.residential_count, 'number');

        // Update Total Current Bill stat
        updateStatCard('#stat-bill', stats.total_current_bill, 'currency');

        // Update Overdue stat
        updateStatCard('#stat-overdue', stats.overdue_count, 'number');
    }

    /**
     * Helper function to update individual stat card with validation
     */
    function updateStatCard(selector, value, type) {
        const el = document.querySelector(`${selector} .text-2xl`);
        if (!el) {
            console.warn(`Stat card element not found: ${selector}`);
            return;
        }

        // Handle undefined/null values
        if (value === undefined || value === null) {
            console.warn(`Stat value is missing for ${selector}:`, value);
            el.textContent = type === 'currency' ? '₱0.00' : '0';
            return;
        }

        // Format based on type
        if (type === 'currency') {
            const amount = parseFloat(value);
            if (isNaN(amount)) {
                console.error(`Invalid currency value for ${selector}:`, value);
                el.textContent = '₱0.00';
                return;
            }
            el.textContent = '₱' + amount.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        } else {
            // Number type
            const num = parseInt(value);
            if (isNaN(num)) {
                console.error(`Invalid number value for ${selector}:`, value);
                el.textContent = '0';
                return;
            }
            el.textContent = num.toLocaleString();
        }
    }

    /**
     * Show error state for stats cards
     */
    function showStatsError() {
        const selectors = ['#stat-total', '#stat-residential', '#stat-bill', '#stat-overdue'];
        selectors.forEach(selector => {
            const el = document.querySelector(`${selector} .text-2xl`);
            if (el) {
                el.textContent = selector === '#stat-bill' ? '₱0.00' : '0';
            }
        });
    }

    /**
     * Fetch and display customer list
     */
    async function loadCustomers() {
        const tbody = document.querySelector('#customerTableBody');
        if (!tbody) return;

        // Show loading state
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                    <div class="flex items-center justify-center">
                        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="ml-3">Loading customers...</span>
                    </div>
                </td>
            </tr>
        `;

        try {
            // Build query parameters
            const params = new URLSearchParams({
                page: currentPage,
                per_page: pageSize
            });

            if (searchTerm) {
                params.append('search', searchTerm);
            }

            if (filterValue) {
                params.append('status', filterValue);
            }

            const response = await fetch(`/customer/list?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            // Update pagination state
            currentPage = data.current_page || 1;
            totalPages = data.last_page || 1;
            totalRecords = data.total || 0;

            // Render table
            renderCustomersTable(data.data || []);

            // Update pagination UI
            updatePagination();

        } catch (error) {
            console.error('Error loading customers:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-red-600 dark:text-red-400">
                            <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="font-medium">Error loading customers</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">${error.message}</p>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    /**
     * Render customer table rows
     */
    function renderCustomersTable(customers) {
        const tbody = document.querySelector('#customerTableBody');
        if (!tbody) return;

        if (!customers || customers.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="font-medium">No customers found</p>
                        <p class="text-sm mt-1">Try adjusting your search or filter criteria</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = customers.map(customer => `
            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <!-- Customer Name & Avatar -->
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                            ${getInitials(customer.customer_name)}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                ${escapeHtml(customer.customer_name)}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                ID: ${escapeHtml(customer.cust_id || 'N/A')}
                            </div>
                        </div>
                    </div>
                </td>

                <!-- Location -->
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-900 dark:text-gray-100">
                        ${escapeHtml(customer.location || 'N/A')}
                    </div>
                </td>

                <!-- Meter Number -->
                <td class="px-4 py-3">
                    <div class="text-sm font-mono text-gray-900 dark:text-gray-100">
                        ${escapeHtml(customer.meter_no || 'N/A')}
                    </div>
                </td>

                <!-- Current Bill -->
                <td class="px-4 py-3 text-right">
                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        ${formatCurrency(customer.current_bill)}
                    </div>
                </td>

                <!-- Status -->
                <td class="px-4 py-3 text-center">
                    ${getStatusBadge(customer.status)}
                </td>

                <!-- Actions -->
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="/customer/details/${customer.cust_id}"
                           class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                           title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    /**
     * Get initials from customer name
     */
    function getInitials(name) {
        if (!name) return '?';

        const parts = name.trim().split(/\s+/);
        if (parts.length === 1) {
            return parts[0].substring(0, 2).toUpperCase();
        }

        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }

    /**
     * Get status badge HTML
     */
    function getStatusBadge(status) {
        const statusUpper = (status || 'UNKNOWN').toUpperCase();

        const badgeClasses = {
            'ACTIVE': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
            'PENDING': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200',
            'INACTIVE': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            'SUSPENDED': 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200'
        };

        const classes = badgeClasses[statusUpper] || badgeClasses['INACTIVE'];

        return `
            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium ${classes}">
                ${escapeHtml(statusUpper)}
            </span>
        `;
    }

    /**
     * Update pagination UI
     */
    function updatePagination() {
        // Update page info
        const currentPageEl = document.getElementById('customerCurrentPage');
        const totalPagesEl = document.getElementById('customerTotalPages');
        const totalRecordsEl = document.getElementById('customerTotalRecords');

        if (currentPageEl) currentPageEl.textContent = currentPage;
        if (totalPagesEl) totalPagesEl.textContent = totalPages;
        if (totalRecordsEl) totalRecordsEl.textContent = totalRecords.toLocaleString();

        // Update buttons
        const prevBtn = document.getElementById('customerPrevBtn');
        const nextBtn = document.getElementById('customerNextBtn');

        if (prevBtn) {
            prevBtn.disabled = currentPage <= 1;
            prevBtn.classList.toggle('opacity-50', currentPage <= 1);
            prevBtn.classList.toggle('cursor-not-allowed', currentPage <= 1);
        }

        if (nextBtn) {
            nextBtn.disabled = currentPage >= totalPages;
            nextBtn.classList.toggle('opacity-50', currentPage >= totalPages);
            nextBtn.classList.toggle('cursor-not-allowed', currentPage >= totalPages);
        }
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    /**
     * Format currency value with validation
     */
    function formatCurrency(value) {
        // Handle undefined, null, or empty values
        if (value === undefined || value === null || value === '') {
            return '₱0.00';
        }

        // Parse and validate number
        const amount = parseFloat(value);
        if (isNaN(amount)) {
            console.warn('Invalid currency value:', value);
            return '₱0.00';
        }

        // Format as currency
        return '₱' + amount.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    /**
     * Search and filter customers
     */
    function searchAndFilterCustomers(search = '', filter = '') {
        searchTerm = search;
        filterValue = filter;
        currentPage = 1; // Reset to first page
        loadCustomers();
    }

    /**
     * Pagination controls
     */
    window.customerPagination = {
        nextPage: function() {
            if (currentPage < totalPages) {
                currentPage++;
                loadCustomers();
            }
        },

        prevPage: function() {
            if (currentPage > 1) {
                currentPage--;
                loadCustomers();
            }
        },

        updatePageSize: function(newSize) {
            pageSize = parseInt(newSize) || 10;
            currentPage = 1; // Reset to first page
            loadCustomers();
        }
    };

    /**
     * Expose search function globally
     */
    window.searchAndFilterCustomers = searchAndFilterCustomers;

    /**
     * Initialize on DOM ready
     */
    function init() {
        // Load initial data
        loadStats();
        loadCustomers();

        // Wire up search input with debounce
        // Note: IDs are generated by action-functions component with tableId prefix
        const searchInput = document.getElementById('customer-list-tbody_search');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const filterSelect = document.getElementById('customer-list-tbody_filter');
                    searchAndFilterCustomers(e.target.value, filterSelect?.value || '');
                }, 300);
            });
        }

        // Wire up filter select
        const filterSelect = document.getElementById('customer-list-tbody_filter');
        if (filterSelect) {
            filterSelect.addEventListener('change', function(e) {
                const searchInput = document.getElementById('customer-list-tbody_search');
                searchAndFilterCustomers(searchInput?.value || '', e.target.value);
            });
        }

        // Wire up clear button
        const clearBtn = document.getElementById('customer-list-tbody_clearBtn');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                const searchInput = document.getElementById('customer-list-tbody_search');
                const filterSelect = document.getElementById('customer-list-tbody_filter');

                if (searchInput) searchInput.value = '';
                if (filterSelect) filterSelect.value = '';

                searchAndFilterCustomers('', '');
            });
        }

        // Wire up page size selector
        const pageSizeSelect = document.getElementById('customerPageSize');
        if (pageSizeSelect) {
            pageSizeSelect.addEventListener('change', function(e) {
                window.customerPagination.updatePageSize(e.target.value);
            });
        }

        // Wire up pagination buttons
        const prevBtn = document.getElementById('customerPrevBtn');
        const nextBtn = document.getElementById('customerNextBtn');

        if (prevBtn) {
            prevBtn.addEventListener('click', () => window.customerPagination.prevPage());
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => window.customerPagination.nextPage());
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
