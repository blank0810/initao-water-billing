<!-- Adjustments Tab Content -->
<div>
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-2"></i>
            <div class="text-sm text-yellow-800 dark:text-yellow-300">
                <strong>Authorized Adjustments Only:</strong> All billing corrections require proper authorization and justification. Complete audit trail is maintained for COA compliance.
            </div>
        </div>
    </div>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <!-- Search -->
            <input type="text" id="adjustmentSearchInput" placeholder="Search by adjustment no, customer..."
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm min-w-[250px]">

            <!-- Type Filter -->
            <select id="adjustmentTypeFilter"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                <option value="">All Types</option>
                <option value="credit">Credit Memo</option>
                <option value="debit">Debit Memo</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="loadAdjustmentsTab()" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm" title="Refresh">
                <i class="fas fa-sync-alt" id="adjustmentRefreshIcon"></i>
            </button>
            <button onclick="openAddAdjustmentModal()" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                <i class="fas fa-plus mr-2"></i>Add Adjustment
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Adjustment No</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Reason</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="adjustmentsTabBody" class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Loading adjustments...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div id="adjustmentsPagination" class="hidden flex justify-between items-center mt-4 flex-wrap gap-4">
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400">Show</span>
            <select id="adjustmentsPageSize" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
            <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="adjustmentsPrevPage()" id="adjustmentsPrevBtn" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                <i class="fas fa-chevron-left mr-1"></i>Previous
            </button>
            <div class="text-sm text-gray-700 dark:text-gray-300 px-3 font-medium">
                Page <span id="adjustmentsCurrentPage">1</span> of <span id="adjustmentsTotalPages">1</span>
            </div>
            <button onclick="adjustmentsNextPage()" id="adjustmentsNextBtn" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                Next<i class="fas fa-chevron-right ml-1"></i>
            </button>
        </div>

        <div class="text-sm text-gray-600 dark:text-gray-400">
            Showing <span class="font-semibold text-gray-900 dark:text-white" id="adjustmentsStartRecord">0</span> to <span class="font-semibold text-gray-900 dark:text-white" id="adjustmentsEndRecord">0</span> of <span class="font-semibold text-gray-900 dark:text-white" id="adjustmentsTotalRecords">0</span> results
        </div>
    </div>
</div>

<script>
let adjustmentsTabInitialized = false;
let adjustmentsTabData = [];
let filteredAdjustmentsTab = [];
let adjustmentsCurrentPage = 1;
let adjustmentsPageSize = 10;
let adjustmentsSearchTimeout = null;

// Initialize when tab is shown
window.initAdjustmentsTab = function() {
    if (!adjustmentsTabInitialized) {
        loadAdjustmentsTab();
        setupAdjustmentsTabListeners();
        adjustmentsTabInitialized = true;
    }
};

function setupAdjustmentsTabListeners() {
    const searchInput = document.getElementById('adjustmentSearchInput');
    const typeFilter = document.getElementById('adjustmentTypeFilter');
    const pageSizeSelect = document.getElementById('adjustmentsPageSize');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(adjustmentsSearchTimeout);
            adjustmentsSearchTimeout = setTimeout(() => {
                adjustmentsCurrentPage = 1;
                filterAndRenderAdjustments();
            }, 300);
        });
    }

    if (typeFilter) {
        typeFilter.addEventListener('change', function() {
            adjustmentsCurrentPage = 1;
            filterAndRenderAdjustments();
        });
    }

    if (pageSizeSelect) {
        pageSizeSelect.addEventListener('change', function() {
            adjustmentsPageSize = parseInt(this.value);
            adjustmentsCurrentPage = 1;
            filterAndRenderAdjustments();
        });
    }

    // Listen for adjustment created event
    document.addEventListener('adjustment-created', () => loadAdjustmentsTab());
}

async function loadAdjustmentsTab() {
    const tbody = document.getElementById('adjustmentsTabBody');
    const refreshIcon = document.getElementById('adjustmentRefreshIcon');

    // Show loading state
    tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Loading adjustments...</td></tr>';
    if (refreshIcon) refreshIcon.classList.add('animate-spin');

    try {
        const response = await fetch('/bill-adjustments');
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        const result = await response.json();

        if (result.success) {
            adjustmentsTabData = result.data || [];
        } else {
            console.error('Failed to load adjustments:', result.message);
            adjustmentsTabData = [];
        }
    } catch (error) {
        console.error('Failed to load adjustments:', error);
        adjustmentsTabData = [];
    } finally {
        if (refreshIcon) refreshIcon.classList.remove('animate-spin');
        filterAndRenderAdjustments();
    }
}

function filterAndRenderAdjustments() {
    const searchQuery = document.getElementById('adjustmentSearchInput')?.value.toLowerCase() || '';
    const typeFilter = document.getElementById('adjustmentTypeFilter')?.value || '';

    // Filter data
    filteredAdjustmentsTab = adjustmentsTabData.filter(adj => {
        // Search filter
        if (searchQuery) {
            const matchesSearch =
                (adj.bill_adjustment_id && adj.bill_adjustment_id.toString().includes(searchQuery)) ||
                (adj.remarks || '').toLowerCase().includes(searchQuery) ||
                (adj.consumer_name || '').toLowerCase().includes(searchQuery) ||
                (adj.account_no || '').toLowerCase().includes(searchQuery);
            if (!matchesSearch) return false;
        }

        // Type filter
        if (typeFilter && adj.direction !== typeFilter) {
            return false;
        }

        return true;
    });

    renderAdjustmentsTable();
}

function renderAdjustmentsTable() {
    const tbody = document.getElementById('adjustmentsTabBody');
    const pagination = document.getElementById('adjustmentsPagination');

    if (filteredAdjustmentsTab.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                    <p class="font-medium">No Adjustments Found</p>
                    <p class="text-sm">No bill adjustments have been recorded yet.</p>
                </td>
            </tr>
        `;
        pagination.classList.add('hidden');
        return;
    }

    // Calculate pagination
    const totalRecords = filteredAdjustmentsTab.length;
    const totalPages = Math.ceil(totalRecords / adjustmentsPageSize) || 1;
    if (adjustmentsCurrentPage > totalPages) adjustmentsCurrentPage = totalPages;
    const startIndex = (adjustmentsCurrentPage - 1) * adjustmentsPageSize;
    const endIndex = Math.min(startIndex + adjustmentsPageSize, totalRecords);
    const paginatedData = filteredAdjustmentsTab.slice(startIndex, endIndex);

    // Render rows
    tbody.innerHTML = paginatedData.map(adj => {
        const typeClass = adj.direction === 'debit'
            ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
            : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
        const amountClass = adj.direction === 'debit'
            ? 'text-red-600 dark:text-red-400'
            : 'text-green-600 dark:text-green-400';
        const arrowIcon = adj.direction === 'debit' ? 'fa-arrow-up' : 'fa-arrow-down';
        const amountPrefix = adj.direction === 'debit' ? '+' : '-';

        let statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
        if (adj.status === 'Active') statusClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
        if (adj.status === 'Voided') statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';

        const voidButton = adj.status !== 'Voided' ? `
            <button onclick="voidAdjustmentTab(${adj.bill_adjustment_id})" class="text-red-600 hover:text-red-800 dark:text-red-400 p-1" title="Void">
                <i class="fas fa-ban"></i>
            </button>
        ` : '';

        return `
            <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-all duration-150">
                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">ADJ-${adj.bill_adjustment_id}</td>
                <td class="px-4 py-3">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${escapeHtml(adj.consumer_name || '-')}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(adj.account_no || '-')}</div>
                </td>
                <td class="px-4 py-3">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${typeClass}">${escapeHtml(adj.type_name || adj.direction)}</span>
                </td>
                <td class="px-4 py-3 text-sm text-right font-medium ${amountClass}">
                    <span class="inline-flex items-center">
                        <i class="fas ${arrowIcon} mr-1 text-xs"></i>
                        ${amountPrefix}₱${Math.abs(adj.amount).toFixed(2)}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate" title="${escapeHtml(adj.remarks || '')}">${escapeHtml(adj.remarks || '-')}</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">${adj.status || 'Pending'}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <button onclick="viewAdjustmentTab(${adj.bill_adjustment_id})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 p-1" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${voidButton}
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    // Update pagination
    pagination.classList.remove('hidden');
    document.getElementById('adjustmentsCurrentPage').textContent = adjustmentsCurrentPage;
    document.getElementById('adjustmentsTotalPages').textContent = totalPages;
    document.getElementById('adjustmentsStartRecord').textContent = totalRecords === 0 ? 0 : startIndex + 1;
    document.getElementById('adjustmentsEndRecord').textContent = endIndex;
    document.getElementById('adjustmentsTotalRecords').textContent = totalRecords;

    // Update button states
    document.getElementById('adjustmentsPrevBtn').disabled = adjustmentsCurrentPage === 1;
    document.getElementById('adjustmentsNextBtn').disabled = adjustmentsCurrentPage === totalPages;
}

function adjustmentsPrevPage() {
    if (adjustmentsCurrentPage > 1) {
        adjustmentsCurrentPage--;
        renderAdjustmentsTable();
    }
}

function adjustmentsNextPage() {
    const totalPages = Math.ceil(filteredAdjustmentsTab.length / adjustmentsPageSize) || 1;
    if (adjustmentsCurrentPage < totalPages) {
        adjustmentsCurrentPage++;
        renderAdjustmentsTable();
    }
}

function viewAdjustmentTab(adjustmentId) {
    const adj = adjustmentsTabData.find(a => a.bill_adjustment_id === adjustmentId);
    if (adj) {
        if (typeof window.viewAdjustmentDetail === 'function') {
            window.viewAdjustmentDetail(adj);
        } else {
            alert('Adjustment ID: ' + adj.bill_adjustment_id + '\nType: ' + (adj.type_name || adj.direction) + '\nAmount: ₱' + (adj.amount ?? 0).toFixed(2) + '\nRemarks: ' + (adj.remarks || 'N/A'));
        }
    }
}

async function voidAdjustmentTab(adjustmentId) {
    if (!confirm('Are you sure you want to void this adjustment? This action cannot be undone.')) {
        return;
    }

    const reason = prompt('Please provide a reason for voiding this adjustment:');
    if (!reason) {
        alert('A reason is required to void an adjustment.');
        return;
    }

    try {
        const response = await fetch(`/bill-adjustments/${adjustmentId}/void`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ reason })
        });

        const result = await response.json();

        if (result.success) {
            if (typeof window.showToast === 'function') {
                window.showToast('Success', result.message, 'success');
            } else {
                alert(result.message);
            }
            loadAdjustmentsTab();
        } else {
            if (typeof window.showToast === 'function') {
                window.showToast('Error', result.message, 'error');
            } else {
                alert(result.message);
            }
        }
    } catch (error) {
        console.error('Failed to void adjustment:', error);
        alert('Failed to void adjustment. Please try again.');
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Expose refresh function
window.refreshAdjustmentsTab = function() {
    loadAdjustmentsTab();
};

// Auto-init if not in a tabbed interface
// The tab system will call initAdjustmentsTab() when needed
// For standalone usage, uncomment the following:
// document.addEventListener('DOMContentLoaded', () => initAdjustmentsTab());
</script>
