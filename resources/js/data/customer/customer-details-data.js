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

    // Store customer ID globally for ledger tab and other components
    window.currentCustomerId = customerId;

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
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            No service connections found
                        </td>
                    </tr>
                `;
            }
        }
    }

    /**
     * Populate service connections table
     * Columns: Account No, Account Type, Meter Reader & Area, Meter No, Date Installed, Status, Actions
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
                    ${escapeHtml(conn.account_type || conn.connection_type || 'N/A')}
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                    <div class="flex flex-col">
                        <span class="font-medium">${escapeHtml(conn.meter_reader || 'N/A')}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(conn.area || 'N/A')}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">
                    ${escapeHtml(conn.meter_no || 'Not Assigned')}
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                    ${escapeHtml(conn.date_installed || 'N/A')}
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${conn.status_badge?.classes || 'bg-gray-100 text-gray-800'}">
                        ${escapeHtml(conn.status_badge?.text || conn.status || 'Unknown')}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <button data-connection-id="${conn.connection_id}"
                            class="view-connection-btn text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        // Attach delegated click listener for connection view buttons
        attachConnectionViewListeners();
    }

    /**
     * Attach delegated event listener for connection view buttons
     * Uses event delegation on the table body to handle dynamically created buttons
     */
    function attachConnectionViewListeners() {
        const tbody = document.getElementById('connections-tbody');
        if (!tbody) return;

        // Remove existing listener to prevent duplicates (using a named function)
        tbody.removeEventListener('click', handleConnectionClick);
        tbody.addEventListener('click', handleConnectionClick);
    }

    /**
     * Handle click events on connection view buttons
     */
    function handleConnectionClick(event) {
        const button = event.target.closest('.view-connection-btn');
        if (!button) return;

        const connectionId = button.dataset.connectionId;

        // Validate connection ID is a positive integer
        if (!connectionId || !/^\d+$/.test(connectionId)) {
            console.error('Invalid connection ID:', connectionId);
            return;
        }

        viewConnectionDetails(parseInt(connectionId, 10));
    }

    /**
     * View connection details (placeholder for modal)
     */
    function viewConnectionDetails(connectionId) {
        console.log('View connection details:', connectionId);
        // TODO: Implement connection details modal if needed
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

        // Initialize ledger tab when first activated
        if (tab === 'ledger' && window.initializeLedgerTab && window.currentCustomerId) {
            window.initializeLedgerTab(window.currentCustomerId);
        }
    };

    /**
     * Fetch customer documents from API
     */
    async function loadCustomerDocuments() {
        try {
            const response = await fetch(`/api/customer/${customerId}/documents`, {
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
                throw new Error(result.message || 'Failed to load customer documents');
            }

            populateDocumentsTable(result.data.documents);

        } catch (error) {
            console.error('Error loading customer documents:', error);
            showDocumentsErrorState(error.message);
        }
    }

    /**
     * Populate documents table with API data
     */
    function populateDocumentsTable(documents) {
        const tbody = document.getElementById('documents-tbody');
        if (!tbody) return;

        if (!documents || documents.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-file-alt text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                        <p>No documents found</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = documents.map(doc => `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-4 py-3">
                    <div class="flex items-center">
                        <i class="fas ${escapeHtml(doc.icon)} text-blue-600 dark:text-blue-400 mr-2"></i>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">${escapeHtml(doc.document_name)}</span>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-900 dark:text-white">
                        <p class="font-medium">${escapeHtml(doc.account_no)}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(doc.connection_type)}</p>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                    ${escapeHtml(doc.generated_at_formatted)}
                </td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${doc.status_badge?.classes || 'bg-gray-100 text-gray-800'}">
                        ${escapeHtml(doc.status_badge?.text || doc.connection_status || 'Unknown')}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="${escapeHtml(doc.view_url)}"
                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                           title="View Document">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="${escapeHtml(doc.print_url)}"
                           class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                           title="Print Document"
                           target="_blank">
                            <i class="fas fa-print"></i>
                        </a>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    /**
     * Show error state for documents
     */
    function showDocumentsErrorState(message) {
        const tbody = document.getElementById('documents-tbody');
        if (!tbody) return;

        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-4 py-8 text-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-2"></i>
                    <p class="text-red-600 dark:text-red-400 font-medium">${escapeHtml(message)}</p>
                </td>
            </tr>
        `;
    }

    /**
     * Initialize on DOM ready
     */
    function init() {
        // Load customer details from API
        loadCustomerDetails();

        // Load customer documents
        loadCustomerDocuments();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
