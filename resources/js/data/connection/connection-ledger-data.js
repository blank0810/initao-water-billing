/**
 * Connection Ledger Tab - Data and interaction handling
 *
 * Scoped to a single connection. Uses conn-ledger- prefixed IDs
 * to avoid conflicts with customer ledger JS.
 */

// State management
let connLedgerState = {
    connectionId: null,
    currentPage: 1,
    perPage: 20,
    filters: {
        period_id: '',
        source_type: ''
    },
    data: null,
    isLoading: false,
    initialized: false
};

/**
 * Initialize connection ledger tab
 */
window.initializeConnectionLedgerTab = function(connectionId) {
    if (connLedgerState.initialized && connLedgerState.connectionId === connectionId) {
        return;
    }

    connLedgerState.connectionId = connectionId;
    connLedgerState.initialized = true;
    loadConnLedgerData();
};

/**
 * Load connection ledger data from API
 */
async function loadConnLedgerData() {
    if (!connLedgerState.connectionId || connLedgerState.isLoading) return;

    connLedgerState.isLoading = true;
    showConnLedgerLoading();

    try {
        const params = new URLSearchParams({
            page: connLedgerState.currentPage,
            per_page: connLedgerState.perPage,
            ...(connLedgerState.filters.period_id && { period_id: connLedgerState.filters.period_id }),
            ...(connLedgerState.filters.source_type && { source_type: connLedgerState.filters.source_type })
        });

        const response = await fetch(`/customer/service-connection/${connLedgerState.connectionId}/ledger?${params}`);
        const result = await response.json();

        if (result.success) {
            connLedgerState.data = result.data;
            populateConnLedgerTable(result.data.entries);
            populateConnLedgerSummary(result.data.summary);
            populateConnLedgerPagination(result.data.pagination);
            populateConnLedgerFilters(result.data.filters);
        } else {
            showConnLedgerError(result.message || 'Failed to load ledger data');
        }
    } catch (error) {
        console.error('Error loading connection ledger:', error);
        showConnLedgerError('An error occurred while loading ledger data');
    } finally {
        connLedgerState.isLoading = false;
    }
}

/**
 * Show loading state
 */
function showConnLedgerLoading() {
    const tbody = document.getElementById('conn-ledger-tbody');
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading ledger data...
                </td>
            </tr>
        `;
    }
}

/**
 * Show error state
 */
function showConnLedgerError(message) {
    const tbody = document.getElementById('conn-ledger-tbody');
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                    <i class="fas fa-exclamation-triangle mr-2"></i> ${connLedger_escapeHtml(message)}
                </td>
            </tr>
        `;
    }
}

/**
 * Populate ledger table with entries
 * Groups entries by date with visual separators
 */
function populateConnLedgerTable(entries) {
    const tbody = document.getElementById('conn-ledger-tbody');
    if (!tbody) return;

    if (!entries || entries.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox mr-2"></i> No ledger entries found
                </td>
            </tr>
        `;
        return;
    }

    let currentDate = null;
    let html = '';

    entries.forEach((entry, index) => {
        const entryDate = entry.txn_date_formatted;
        const isNewDateGroup = entryDate !== currentDate;
        const isFirstEntry = index === 0;

        // Date separator row
        if (isNewDateGroup) {
            currentDate = entryDate;
            html += `
                <tr class="bg-gray-50 dark:bg-gray-700/50 ${!isFirstEntry ? 'border-t-2 border-gray-300 dark:border-gray-600' : ''}">
                    <td colspan="6" class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-day text-blue-500 dark:text-blue-400 text-xs"></i>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">${entryDate}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">(${connLedger_formatRelativeDate(entry.txn_date)})</span>
                        </div>
                    </td>
                </tr>
            `;
        }

        // Entry row
        const isCancelledEntry = entry.status?.stat_desc === 'CANCELLED';
        const isReversalEntry = entry.source_type === 'REVERSAL';
        const rowClasses = isCancelledEntry
            ? 'bg-red-50/50 dark:bg-red-900/10 opacity-60 border-l-4 border-l-gray-300'
            : isReversalEntry
                ? 'bg-amber-50/50 dark:bg-amber-900/10 border-l-4 border-l-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20'
                : `hover:bg-blue-50 dark:hover:bg-gray-700 border-l-4 ${entry.credit > 0 ? 'border-l-green-400' : 'border-l-red-400'}`;

        html += `
            <tr class="${rowClasses} transition-colors">
                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                    <span class="text-xs">${connLedger_formatTimeOnly(entry.post_ts)}</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${entry.source_type_badge.bg} ${entry.source_type_badge.text}">
                        ${entry.source_type_label}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate" title="${connLedger_escapeHtml(entry.description)}">
                    ${connLedger_escapeHtml(entry.description)}
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
            </tr>
        `;
    });

    tbody.innerHTML = html;
}

/**
 * Populate summary section
 */
function populateConnLedgerSummary(summary) {
    const totalDebitEl = document.getElementById('conn-ledger-total-debit');
    const totalCreditEl = document.getElementById('conn-ledger-total-credit');
    const netBalanceEl = document.getElementById('conn-ledger-net-balance');

    if (totalDebitEl) totalDebitEl.textContent = summary.total_debit_formatted;
    if (totalCreditEl) totalCreditEl.textContent = summary.total_credit_formatted;

    if (netBalanceEl) {
        netBalanceEl.textContent = summary.net_balance_formatted;
        netBalanceEl.classList.remove('text-red-600', 'text-green-600', 'text-blue-600');
        netBalanceEl.classList.add(summary.balance_class);
    }
}

/**
 * Populate pagination
 */
function populateConnLedgerPagination(pagination) {
    const showingStart = document.getElementById('conn-ledger-showing-start');
    const showingEnd = document.getElementById('conn-ledger-showing-end');
    const totalEl = document.getElementById('conn-ledger-total');

    if (showingStart) {
        showingStart.textContent = pagination.total === 0 ? 0 : ((pagination.current_page - 1) * pagination.per_page) + 1;
    }
    if (showingEnd) {
        showingEnd.textContent = Math.min(pagination.current_page * pagination.per_page, pagination.total);
    }
    if (totalEl) {
        totalEl.textContent = pagination.total;
    }

    const buttonsContainer = document.getElementById('conn-ledger-pagination-buttons');
    if (!buttonsContainer) return;

    let buttons = '';

    // Previous button
    buttons += `
        <button onclick="goToConnectionLedgerPage(${pagination.current_page - 1})"
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
            <button onclick="goToConnectionLedgerPage(${i})"
                class="px-3 py-1 text-sm rounded-lg ${i === pagination.current_page
                    ? 'bg-blue-600 text-white'
                    : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500'}">
                ${i}
            </button>
        `;
    }

    // Next button
    buttons += `
        <button onclick="goToConnectionLedgerPage(${pagination.current_page + 1})"
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
function populateConnLedgerFilters(filters) {
    const periodSelect = document.getElementById('conn-ledger-period-filter');
    if (periodSelect && periodSelect.options.length <= 1) {
        filters.periods.forEach(period => {
            const option = document.createElement('option');
            option.value = period.per_id;
            option.textContent = period.label;
            periodSelect.appendChild(option);
        });
    }

    const typeSelect = document.getElementById('conn-ledger-type-filter');
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
 * Go to specific page
 */
window.goToConnectionLedgerPage = function(page) {
    if (page < 1 || connLedgerState.isLoading) return;
    if (connLedgerState.data && page > connLedgerState.data.pagination.last_page) return;

    connLedgerState.currentPage = page;
    loadConnLedgerData();
};

/**
 * Filter based on dropdown selections
 */
window.filterConnectionLedger = function() {
    connLedgerState.filters.period_id = document.getElementById('conn-ledger-period-filter')?.value || '';
    connLedgerState.filters.source_type = document.getElementById('conn-ledger-type-filter')?.value || '';
    connLedgerState.currentPage = 1;
    loadConnLedgerData();
};

/**
 * Reset all filters
 */
window.resetConnectionLedgerFilters = function() {
    const periodSelect = document.getElementById('conn-ledger-period-filter');
    const typeSelect = document.getElementById('conn-ledger-type-filter');

    if (periodSelect) periodSelect.value = '';
    if (typeSelect) typeSelect.value = '';

    connLedgerState.filters = { period_id: '', source_type: '' };
    connLedgerState.currentPage = 1;
    loadConnLedgerData();
};

// --- Helper functions (prefixed to avoid conflicts) ---

function connLedger_formatRelativeDate(dateStr) {
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

function connLedger_formatTimeOnly(timestamp) {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
}

function connLedger_escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
