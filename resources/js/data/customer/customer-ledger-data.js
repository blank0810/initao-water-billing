/**
 * Customer Ledger Tab - Data and interaction handling
 */

// State management
let ledgerState = {
    customerId: null,
    currentPage: 1,
    perPage: 20,
    filters: {
        connection_id: '',
        period_id: '',
        source_type: ''
    },
    data: null,
    isLoading: false,
    initialized: false
};

/**
 * Initialize ledger tab when activated
 */
window.initializeLedgerTab = function(customerId) {
    // Only load if not already initialized or customer changed
    if (ledgerState.initialized && ledgerState.customerId === customerId) {
        return;
    }

    ledgerState.customerId = customerId;
    ledgerState.initialized = true;
    loadLedgerData();
};

/**
 * Load ledger data from API
 */
async function loadLedgerData() {
    if (!ledgerState.customerId || ledgerState.isLoading) return;

    ledgerState.isLoading = true;
    showLedgerLoading();

    try {
        const params = new URLSearchParams({
            page: ledgerState.currentPage,
            per_page: ledgerState.perPage,
            ...(ledgerState.filters.connection_id && { connection_id: ledgerState.filters.connection_id }),
            ...(ledgerState.filters.period_id && { period_id: ledgerState.filters.period_id }),
            ...(ledgerState.filters.source_type && { source_type: ledgerState.filters.source_type })
        });

        const response = await fetch(`/api/customer/${ledgerState.customerId}/ledger?${params}`);
        const result = await response.json();

        if (result.success) {
            ledgerState.data = result.data;
            populateLedgerTable(result.data.entries);
            populateLedgerSummary(result.data.summary);
            populateLedgerPagination(result.data.pagination);
            populateLedgerFilters(result.data.filters);
        } else {
            showLedgerError(result.message || 'Failed to load ledger data');
        }
    } catch (error) {
        console.error('Error loading ledger:', error);
        showLedgerError('An error occurred while loading ledger data');
    } finally {
        ledgerState.isLoading = false;
    }
}

/**
 * Show loading state in ledger table
 */
function showLedgerLoading() {
    const tbody = document.getElementById('ledger-tbody');
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading ledger data...
                </td>
            </tr>
        `;
    }
}

/**
 * Show error state in ledger table
 */
function showLedgerError(message) {
    const tbody = document.getElementById('ledger-tbody');
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                    <i class="fas fa-exclamation-triangle mr-2"></i> ${message}
                </td>
            </tr>
        `;
    }
}

/**
 * Populate ledger table with entries
 * Groups entries by date with visual separators for better UX
 */
function populateLedgerTable(entries) {
    const tbody = document.getElementById('ledger-tbody');
    if (!tbody) return;

    if (!entries || entries.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox mr-2"></i> No ledger entries found
                </td>
            </tr>
        `;
        return;
    }

    // Group entries by date for visual separation
    let currentDate = null;
    let html = '';

    entries.forEach((entry, index) => {
        const entryDate = entry.txn_date_formatted;
        const isNewDateGroup = entryDate !== currentDate;
        const isFirstEntry = index === 0;

        // Add date separator row when date changes
        if (isNewDateGroup) {
            currentDate = entryDate;
            html += `
                <tr class="bg-gray-50 dark:bg-gray-700/50 ${!isFirstEntry ? 'border-t-2 border-gray-300 dark:border-gray-600' : ''}">
                    <td colspan="8" class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-day text-blue-500 dark:text-blue-400 text-xs"></i>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">${entryDate}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">(${formatRelativeDate(entry.txn_date)})</span>
                        </div>
                    </td>
                </tr>
            `;
        }

        // Entry row with improved styling
        const isCancelledEntry = entry.status?.stat_desc === 'CANCELLED';
        const isReversalEntry = entry.source_type === 'REVERSAL';
        const rowClasses = isCancelledEntry
            ? 'bg-red-50/50 dark:bg-red-900/10 opacity-60 border-l-4 border-l-gray-300'
            : isReversalEntry
                ? 'bg-amber-50/50 dark:bg-amber-900/10 border-l-4 border-l-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20'
                : `hover:bg-blue-50 dark:hover:bg-gray-700 border-l-4 ${entry.credit > 0 ? 'border-l-green-400' : 'border-l-red-400'}`;
        html += `
            <tr class="${rowClasses} transition-colors cursor-pointer" onclick="showLedgerEntryDetails(${entry.ledger_entry_id})">`;
                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                    <span class="text-xs">${formatTimeOnly(entry.post_ts)}</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${entry.source_type_badge.bg} ${entry.source_type_badge.text}">
                        ${entry.source_type_label}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate" title="${escapeHtml(entry.description)}">
                    ${escapeHtml(entry.description)}
                </td>
                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                    ${entry.connection ? entry.connection.account_no : '-'}
                </td>
                <td class="px-4 py-3 text-sm text-right font-medium ${entry.debit > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400'} whitespace-nowrap">
                    ${entry.debit > 0 ? entry.debit_formatted : '-'}
                </td>
                <td class="px-4 py-3 text-sm text-right font-medium ${entry.credit > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400'} whitespace-nowrap">
                    ${entry.credit > 0 ? entry.credit_formatted : '-'}
                </td>
                <td class="px-4 py-3 text-sm text-right font-semibold ${entry.balance_class} whitespace-nowrap">
                    ${entry.running_balance_formatted}
                </td>
                <td class="px-4 py-3 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center gap-1">
                        <button onclick="event.stopPropagation(); showLedgerEntryDetails(${entry.ledger_entry_id})"
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${entry.source_type === 'PAYMENT' && entry.status?.stat_desc !== 'CANCELLED' && window.canVoidPayments ? `
                        <button onclick="event.stopPropagation(); cancelPaymentFromLedger(${entry.source_id})"
                            class="text-gray-400 hover:text-red-600 dark:text-gray-500 dark:hover:text-red-400 ml-1"
                            title="Cancel Payment">
                            <i class="fas fa-ban"></i>
                        </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}

/**
 * Format date as relative (Today, Yesterday, or date)
 */
function formatRelativeDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);

    if (date.toDateString() === today.toDateString()) {
        return 'Today';
    } else if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday';
    } else {
        const diffTime = Math.abs(today - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return `${diffDays} days ago`;
    }
}

/**
 * Format timestamp to time only (HH:MM AM/PM)
 */
function formatTimeOnly(timestamp) {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Populate ledger summary section
 */
function populateLedgerSummary(summary) {
    const totalDebitEl = document.getElementById('ledger-total-debit');
    const totalCreditEl = document.getElementById('ledger-total-credit');
    const netBalanceEl = document.getElementById('ledger-net-balance');

    if (totalDebitEl) totalDebitEl.textContent = summary.total_debit_formatted;
    if (totalCreditEl) totalCreditEl.textContent = summary.total_credit_formatted;

    if (netBalanceEl) {
        netBalanceEl.textContent = summary.net_balance_formatted;
        // Remove existing color classes and add new one
        netBalanceEl.classList.remove('text-red-600', 'text-green-600', 'text-blue-600');
        netBalanceEl.classList.add(summary.balance_class);
    }
}

/**
 * Populate ledger pagination
 */
function populateLedgerPagination(pagination) {
    const showingStart = document.getElementById('ledger-showing-start');
    const showingEnd = document.getElementById('ledger-showing-end');
    const totalEl = document.getElementById('ledger-total');

    if (showingStart) {
        showingStart.textContent = pagination.total === 0 ? 0 : ((pagination.current_page - 1) * pagination.per_page) + 1;
    }
    if (showingEnd) {
        showingEnd.textContent = Math.min(pagination.current_page * pagination.per_page, pagination.total);
    }
    if (totalEl) {
        totalEl.textContent = pagination.total;
    }

    const buttonsContainer = document.getElementById('ledger-pagination-buttons');
    if (!buttonsContainer) return;

    let buttons = '';

    // Previous button
    buttons += `
        <button onclick="goToLedgerPage(${pagination.current_page - 1})"
            class="px-3 py-1 text-sm rounded-lg ${pagination.current_page === 1
                ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700'
                : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500'}"
            ${pagination.current_page === 1 ? 'disabled' : ''}>
            <i class="fas fa-chevron-left"></i>
        </button>
    `;

    // Page numbers (show max 5)
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, startPage + 4);

    for (let i = startPage; i <= endPage; i++) {
        buttons += `
            <button onclick="goToLedgerPage(${i})"
                class="px-3 py-1 text-sm rounded-lg ${i === pagination.current_page
                    ? 'bg-blue-600 text-white'
                    : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500'}">
                ${i}
            </button>
        `;
    }

    // Next button
    buttons += `
        <button onclick="goToLedgerPage(${pagination.current_page + 1})"
            class="px-3 py-1 text-sm rounded-lg ${pagination.current_page === pagination.last_page || pagination.last_page === 0
                ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700'
                : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500'}"
            ${pagination.current_page === pagination.last_page || pagination.last_page === 0 ? 'disabled' : ''}>
            <i class="fas fa-chevron-right"></i>
        </button>
    `;

    buttonsContainer.innerHTML = buttons;
}

/**
 * Populate filter dropdowns (only on first load)
 */
function populateLedgerFilters(filters) {
    // Only populate if dropdowns are empty (first load)
    const connectionSelect = document.getElementById('ledger-connection-filter');
    if (connectionSelect && connectionSelect.options.length <= 1) {
        filters.connections.forEach(conn => {
            const option = document.createElement('option');
            option.value = conn.connection_id;
            option.textContent = conn.account_no;
            connectionSelect.appendChild(option);
        });
    }

    const periodSelect = document.getElementById('ledger-period-filter');
    if (periodSelect && periodSelect.options.length <= 1) {
        filters.periods.forEach(period => {
            const option = document.createElement('option');
            option.value = period.per_id;
            option.textContent = period.label;
            periodSelect.appendChild(option);
        });
    }

    const typeSelect = document.getElementById('ledger-type-filter');
    if (typeSelect && typeSelect.options.length <= 1) {
        filters.types.forEach(type => {
            const option = document.createElement('option');
            option.value = type.value;
            option.textContent = type.label;
            typeSelect.appendChild(option);
        });
    }
}

/**
 * Go to specific ledger page
 */
window.goToLedgerPage = function(page) {
    if (page < 1 || ledgerState.isLoading) return;
    if (ledgerState.data && page > ledgerState.data.pagination.last_page) return;

    ledgerState.currentPage = page;
    loadLedgerData();
};

/**
 * Filter ledger based on dropdown selections
 */
window.filterLedger = function() {
    ledgerState.filters.connection_id = document.getElementById('ledger-connection-filter')?.value || '';
    ledgerState.filters.period_id = document.getElementById('ledger-period-filter')?.value || '';
    ledgerState.filters.source_type = document.getElementById('ledger-type-filter')?.value || '';
    ledgerState.currentPage = 1; // Reset to first page on filter
    loadLedgerData();
};

/**
 * Reset all ledger filters
 */
window.resetLedgerFilters = function() {
    const connectionSelect = document.getElementById('ledger-connection-filter');
    const periodSelect = document.getElementById('ledger-period-filter');
    const typeSelect = document.getElementById('ledger-type-filter');

    if (connectionSelect) connectionSelect.value = '';
    if (periodSelect) periodSelect.value = '';
    if (typeSelect) typeSelect.value = '';

    ledgerState.filters = { connection_id: '', period_id: '', source_type: '' };
    ledgerState.currentPage = 1;
    loadLedgerData();
};

/**
 * Show ledger entry details modal
 */
window.showLedgerEntryDetails = async function(entryId) {
    const modal = document.getElementById('ledger-entry-modal');
    const loading = document.getElementById('ledger-modal-loading');
    const details = document.getElementById('ledger-modal-details');

    if (!modal) return;

    // Show modal with loading state
    modal.classList.remove('hidden');
    if (loading) loading.classList.remove('hidden');
    if (details) details.classList.add('hidden');

    try {
        const response = await fetch(`/api/customer/ledger/${entryId}`);
        const result = await response.json();

        if (result.success) {
            populateLedgerEntryModal(result.data);
            if (loading) loading.classList.add('hidden');
            if (details) details.classList.remove('hidden');
        } else {
            closeLedgerEntryModal();
            alert(result.message || 'Failed to load entry details');
        }
    } catch (error) {
        console.error('Error loading entry details:', error);
        closeLedgerEntryModal();
        alert('An error occurred while loading entry details');
    }
};

/**
 * Populate ledger entry modal with data
 */
function populateLedgerEntryModal(data) {
    const entry = data.entry;

    // Basic info
    const txnDateEl = document.getElementById('modal-txn-date');
    const txnTypeEl = document.getElementById('modal-txn-type');
    const descriptionEl = document.getElementById('modal-description');
    const debitEl = document.getElementById('modal-debit');
    const creditEl = document.getElementById('modal-credit');

    if (txnDateEl) txnDateEl.textContent = entry.txn_date_formatted;
    if (txnTypeEl) {
        txnTypeEl.innerHTML = `
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${entry.source_type_badge.bg} ${entry.source_type_badge.text}">
                ${entry.source_type_label}
            </span>
        `;
    }
    if (descriptionEl) descriptionEl.textContent = entry.description;
    if (debitEl) debitEl.textContent = entry.debit_formatted;
    if (creditEl) creditEl.textContent = entry.credit_formatted;

    // Source document section
    const sourceSection = document.getElementById('modal-source-section');
    const sourceContent = document.getElementById('modal-source-content');
    const printBtn = document.getElementById('modal-print-btn');

    if (data.source_details && sourceSection && sourceContent) {
        sourceSection.classList.remove('hidden');
        sourceContent.innerHTML = formatSourceDetails(data.source_details);
        if (printBtn) printBtn.classList.remove('hidden');
    } else {
        if (sourceSection) sourceSection.classList.add('hidden');
        if (printBtn) printBtn.classList.add('hidden');
    }

    // Connection section
    const connectionSection = document.getElementById('modal-connection-section');
    const accountNoEl = document.getElementById('modal-account-no');
    const accountTypeEl = document.getElementById('modal-account-type');

    if (data.connection_details && connectionSection) {
        connectionSection.classList.remove('hidden');
        if (accountNoEl) accountNoEl.textContent = data.connection_details.account_no || '-';
        if (accountTypeEl) accountTypeEl.textContent = data.connection_details.account_type || '-';
    } else {
        if (connectionSection) connectionSection.classList.add('hidden');
    }

    // Audit info
    const createdByEl = document.getElementById('modal-created-by');
    const postTsEl = document.getElementById('modal-post-ts');

    if (createdByEl) createdByEl.textContent = data.audit_info.created_by;
    if (postTsEl) postTsEl.textContent = data.audit_info.post_timestamp;
}

/**
 * Format source document details for display
 * All values are escaped to prevent XSS attacks
 */
function formatSourceDetails(source) {
    if (source.type === 'BILL') {
        return `
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Billing Period</p>
                    <p class="font-medium text-gray-900 dark:text-white">${escapeHtml(source.period)}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Due Date</p>
                    <p class="font-medium text-gray-900 dark:text-white">${escapeHtml(source.due_date)}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Previous Reading</p>
                    <p class="font-medium text-gray-900 dark:text-white">${escapeHtml(source.prev_reading)}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Current Reading</p>
                    <p class="font-medium text-gray-900 dark:text-white">${escapeHtml(source.curr_reading)}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Consumption</p>
                    <p class="font-medium text-gray-900 dark:text-white">${escapeHtml(source.consumption)}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Amount</p>
                    <p class="font-semibold text-red-600">${escapeHtml(source.total_amount)}</p>
                </div>
            </div>
        `;
    } else if (source.type === 'CHARGE') {
        return `
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="col-span-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Charge Item</p>
                    <p class="font-medium text-gray-900 dark:text-white">${escapeHtml(source.charge_item)}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Description</p>
                    <p class="font-medium text-gray-900 dark:text-white">${escapeHtml(source.description) || '-'}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Quantity x Unit Price</p>
                    <p class="font-medium text-gray-900 dark:text-white">${escapeHtml(source.quantity)} x ${escapeHtml(source.unit_amount)}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Amount</p>
                    <p class="font-semibold text-red-600">${escapeHtml(source.total_amount)}</p>
                </div>
            </div>
        `;
    } else if (source.type === 'PAYMENT') {
        return `
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Receipt Number</p>
                    <p class="font-mono font-medium text-gray-900 dark:text-white">${escapeHtml(source.receipt_no)}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Payment Date</p>
                    <p class="font-medium text-gray-900 dark:text-white">${escapeHtml(source.payment_date)}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Amount Received</p>
                    <p class="font-semibold text-green-600">${escapeHtml(source.amount_received)}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Processed By</p>
                    <p class="font-medium text-gray-900 dark:text-white">${escapeHtml(source.processed_by)}</p>
                </div>
            </div>
        `;
    }
    return '<p class="text-gray-500">No additional details available</p>';
}

/**
 * Close ledger entry modal
 */
window.closeLedgerEntryModal = function() {
    const modal = document.getElementById('ledger-entry-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
};

/**
 * Cancel a payment from the ledger view
 * Prompts for reason and calls the cancel API endpoint
 */
window.cancelPaymentFromLedger = async function(paymentId) {
    const reason = prompt('Please provide a reason for cancelling this payment:');
    if (!reason || !reason.trim()) return;

    if (!confirm('Are you sure you want to cancel this payment? This will reverse all ledger entries and make bills/charges available for payment again.')) {
        return;
    }

    try {
        const response = await fetch(`/payment/${paymentId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ reason: reason.trim() }),
        });

        const result = await response.json();

        if (result.success) {
            alert('Payment cancelled successfully.');
            loadLedgerData(); // Reload to show updated entries
        } else {
            alert(result.message || 'Failed to cancel payment.');
        }
    } catch (error) {
        console.error('Error cancelling payment:', error);
        alert('Network error. Please try again.');
    }
};

/**
 * Toggle export dropdown menu
 */
window.toggleExportDropdown = function() {
    const dropdown = document.getElementById('export-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
};

/**
 * Close export dropdown when clicking outside
 */
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('export-dropdown');
    const button = document.getElementById('export-button');
    if (dropdown && button && !button.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});

/**
 * Export ledger as CSV (server-side generation with full data)
 */
window.exportLedgerCsv = function() {
    if (!ledgerState.customerId) {
        alert('No customer selected');
        return;
    }

    // Build URL with filters
    const params = new URLSearchParams();
    if (ledgerState.filters.connection_id) params.append('connection_id', ledgerState.filters.connection_id);

    const url = `/api/customer/${ledgerState.customerId}/ledger/export/csv?${params}`;
    window.location.href = url;

    // Close dropdown
    const dropdown = document.getElementById('export-dropdown');
    if (dropdown) dropdown.classList.add('hidden');
};

/**
 * Export ledger as PDF (opens printable statement in new tab)
 */
window.exportLedgerPdf = function() {
    if (!ledgerState.customerId) {
        alert('No customer selected');
        return;
    }

    // Build URL with filters
    const params = new URLSearchParams();
    if (ledgerState.filters.connection_id) params.append('connection_id', ledgerState.filters.connection_id);

    const url = `/api/customer/${ledgerState.customerId}/ledger/export/pdf?${params}`;
    window.open(url, '_blank');

    // Close dropdown
    const dropdown = document.getElementById('export-dropdown');
    if (dropdown) dropdown.classList.add('hidden');
};

/**
 * Legacy export function (kept for backwards compatibility)
 */
window.exportLedger = function() {
    toggleExportDropdown();
};

/**
 * Print ledger entry (opens print dialog)
 */
window.printLedgerEntry = function() {
    window.print();
};
