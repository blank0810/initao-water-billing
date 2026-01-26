/**
 * Customer Details - Real API Implementation
 *
 * Fetches customer details from backend API and populates the details page
 */

(function() {
    'use strict';

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Get customer ID from URL
    const pathParts = window.location.pathname.split('/');
    const customerId = pathParts[pathParts.length - 1];

    /**
     * Fetch customer details from API
     */
    async function loadCustomerDetails() {
        try {
            showLoadingState();

            const response = await fetch(`/api/customer/${customerId}/details`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                if (response.status === 404) {
                    throw new Error('Customer not found');
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Failed to load customer details');
            }

            populateCustomerDetails(result.data);

        } catch (error) {
            console.error('Error loading customer details:', error);
            showErrorState(error.message);
        }
    }

    /**
     * Show loading state
     */
    function showLoadingState() {
        // Customer Info
        document.getElementById('consumer-id').textContent = 'Loading...';
        document.getElementById('consumer-name').textContent = 'Loading...';
        document.getElementById('consumer-address').textContent = 'Loading...';

        // Meter & Billing
        document.getElementById('consumer-meter').textContent = 'Loading...';
        document.getElementById('consumer-rate').textContent = 'Loading...';
        document.getElementById('consumer-bill').textContent = 'Loading...';

        // Account Status
        document.getElementById('consumer-status').textContent = 'Loading...';
        document.getElementById('consumer-ledger').textContent = 'Loading...';
        document.getElementById('consumer-updated').textContent = 'Loading...';
    }

    /**
     * Show error state
     */
    function showErrorState(message) {
        const errorHTML = `
            <div class="p-6 text-center">
                <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-4"></i>
                <p class="text-red-600 font-medium">${escapeHtml(message)}</p>
                <button onclick="location.href='/customer/list'" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Back to Customer List
                </button>
            </div>
        `;

        // Show error in customer info card
        document.getElementById('consumer-id').innerHTML = errorHTML;
    }

    /**
     * Populate customer details with API data
     */
    function populateCustomerDetails(data) {
        // Customer Information
        if (data.customer_info) {
            const info = data.customer_info;
            document.getElementById('consumer-id').textContent = info.customer_code || info.cust_id;
            document.getElementById('consumer-name').textContent = info.full_name || 'N/A';
            document.getElementById('consumer-address').textContent = info.address || 'N/A';
        }

        // Meter & Billing
        if (data.meter_billing) {
            const billing = data.meter_billing;
            document.getElementById('consumer-meter').textContent = billing.meter_no || 'Not Assigned';
            document.getElementById('consumer-rate').textContent = billing.rate_class || 'N/A';
            document.getElementById('consumer-bill').textContent = billing.total_bill_formatted || '₱0.00';
        }

        // Account Status
        if (data.account_status) {
            const status = data.account_status;

            // Status badge
            const statusEl = document.getElementById('consumer-status');
            if (status.status_badge) {
                statusEl.textContent = status.status_badge.text;
                statusEl.className = `inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${status.status_badge.classes}`;
            } else {
                statusEl.textContent = status.status || 'Unknown';
            }

            // Ledger balance
            document.getElementById('consumer-ledger').textContent = status.ledger_balance_formatted || '₱0.00';

            // Last updated
            document.getElementById('consumer-updated').textContent = status.last_updated_formatted || 'N/A';
        }

        // Service Connections Table
        if (data.service_connections && data.service_connections.length > 0) {
            populateServiceConnections(data.service_connections);
        } else {
            const tbody = document.getElementById('connections-tbody');
            if (tbody) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            No service connections found
                        </td>
                    </tr>
                `;
            }
        }
    }

    /**
     * Populate service connections table
     */
    function populateServiceConnections(connections) {
        const tbody = document.getElementById('connections-tbody');
        if (!tbody) return;

        tbody.innerHTML = connections.map(conn => `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                    ${escapeHtml(conn.account_no || 'N/A')}
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                    ${escapeHtml(conn.connection_type || 'N/A')}
                </td>
                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">
                    ${escapeHtml(conn.meter_no || 'Not Assigned')}
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                    ${escapeHtml(conn.area || 'N/A')}
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${conn.status_badge?.classes || 'bg-gray-100 text-gray-800'}">
                        ${escapeHtml(conn.status_badge?.text || conn.status || 'Unknown')}
                    </span>
                </td>
                <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-white">
                    ${escapeHtml(conn.started_at || 'N/A')}
                </td>
            </tr>
        `).join('');
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
     * Tab switching functionality
     */
    window.switchTab = function(tab) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
        });

        // Reset all tab buttons
        document.querySelectorAll('.tab-button').forEach(el => {
            el.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            el.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });

        // Show selected tab content
        const contentEl = document.getElementById(tab + '-content');
        if (contentEl) {
            contentEl.classList.remove('hidden');
        }

        // Activate selected tab button
        const tabBtn = document.getElementById('tab-' + tab);
        if (tabBtn) {
            tabBtn.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            tabBtn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        }
    };

    /**
     * Initialize on DOM ready
     */
    function init() {
        // Load customer details from API
        loadCustomerDetails();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
