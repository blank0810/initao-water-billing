<div class="mb-4 flex gap-4 items-center">
    <input type="text" id="billHistorySearch" placeholder="Search bills..." class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
    <select id="billStatusFilter" onchange="filterBillHistory()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
        <option value="">All Status</option>
        <option value="1">Active (Unpaid)</option>
        <option value="2">Paid</option>
        <option value="3">Cancelled</option>
        <option value="4">Overdue</option>
        <option value="5">Adjusted</option>
    </select>
    <div class="flex gap-2">
        <button onclick="billing.exportToExcel('billHistoryTableWrapper', 'bill-history')" class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm" title="Export to Excel">
            <i class="fas fa-file-excel"></i>
        </button>
        <button onclick="billing.exportToPDF('billHistoryTableWrapper', 'Bill History')" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm" title="Export to PDF">
            <i class="fas fa-file-pdf"></i>
        </button>
        <button onclick="billing.printTable('billHistoryTableWrapper')" class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm" title="Print">
            <i class="fas fa-print"></i>
        </button>
    </div>
</div>

<x-ui.card>
    <div id="billHistoryTableWrapper" class="overflow-x-auto border rounded-lg shadow-sm bg-white dark:bg-gray-800">
        <table id="billHistoryTableFull" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 z-10">
                <tr>
                    <th onclick="billing.sortData('billHistory', 'bill_id'); renderBillHistory();" class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600">Bill ID <i class="fas fa-sort"></i></th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Consumer</th>
                    <th onclick="billing.sortData('billHistory', 'consumption'); renderBillHistory();" class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600">Consumption <i class="fas fa-sort"></i></th>
                    <th onclick="billing.sortData('billHistory', 'total_amount'); renderBillHistory();" class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600">Amount <i class="fas fa-sort"></i></th>
                    <th onclick="billing.sortData('billHistory', 'due_date'); renderBillHistory();" class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600">Due Date <i class="fas fa-sort"></i></th>
                    <th onclick="billing.sortData('billHistory', 'stat_id'); renderBillHistory();" class="px-4 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600">Status <i class="fas fa-sort"></i></th>
                </tr>
            </thead>
            <tbody id="billHistoryTable" class="divide-y divide-gray-200 dark:divide-gray-700">
            </tbody>
        </table>
    </div>
    <div id="billHistoryPagination"></div>
</x-ui.card>

<script>
function renderBillHistory() {
    if (!window.billingData) {
        setTimeout(renderBillHistory, 100);
        return;
    }
    const { waterBillHistory, consumers } = window.billingData;
    const state = billing.paginationState.billHistory;
    
    if (state.data.length === 0) {
        state.data = [...waterBillHistory];
    }
    
    const paginatedData = billing.paginate(state.data, state.page, state.perPage);
    const tbody = document.getElementById('billHistoryTable');
    tbody.innerHTML = paginatedData.map(bill => {
        const consumer = consumers.find(c => c.connection_id === bill.connection_id);
        const statusMap = { 1: 'Active', 2: 'Paid', 3: 'Cancelled', 4: 'Overdue', 5: 'Adjusted' };
        const statusClass = { 1: 'bg-yellow-100 text-yellow-800', 2: 'bg-green-100 text-green-800', 3: 'bg-gray-100 text-gray-800', 4: 'bg-red-100 text-red-800', 5: 'bg-blue-100 text-blue-800' };
        
        return `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 text-sm font-mono text-gray-900 dark:text-gray-100">${bill.bill_id}</td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">${consumer?.name || 'N/A'}</td>
                <td class="px-6 py-4 text-sm text-right text-gray-900 dark:text-gray-100">${bill.consumption.toFixed(2)} m³</td>
                <td class="px-6 py-4 text-sm text-right font-medium text-gray-900 dark:text-gray-100">₱${bill.total_amount.toFixed(2)}</td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">${bill.due_date}</td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass[bill.stat_id]}">${statusMap[bill.stat_id]}</span>
                </td>
            </tr>
        `;
    }).join('');
    
    billing.paginationState.billHistory.data = state.data;
    billing.changePage('billHistory', state.page);
}

function filterBillHistory() {
    const filter = document.getElementById('billStatusFilter').value;
    const { waterBillHistory, consumers } = window.billingData;
    const filtered = filter ? waterBillHistory.filter(b => b.stat_id == filter) : waterBillHistory;
    
    const tbody = document.getElementById('billHistoryTable');
    tbody.innerHTML = filtered.map(bill => {
        const consumer = consumers.find(c => c.connection_id === bill.connection_id);
        const statusMap = { 1: 'Active', 2: 'Paid', 3: 'Cancelled', 4: 'Overdue', 5: 'Adjusted' };
        const statusClass = { 1: 'bg-yellow-100 text-yellow-800', 2: 'bg-green-100 text-green-800', 3: 'bg-gray-100 text-gray-800', 4: 'bg-red-100 text-red-800', 5: 'bg-blue-100 text-blue-800' };
        
        return `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 text-sm font-mono text-gray-900 dark:text-gray-100">${bill.bill_id}</td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">${consumer?.name || 'N/A'}</td>
                <td class="px-6 py-4 text-sm text-right text-gray-900 dark:text-gray-100">${bill.consumption.toFixed(2)} m³</td>
                <td class="px-6 py-4 text-sm text-right font-medium text-gray-900 dark:text-gray-100">₱${bill.total_amount.toFixed(2)}</td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">${bill.due_date}</td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass[bill.stat_id]}">${statusMap[bill.stat_id]}</span>
                </td>
            </tr>
        `;
    }).join('');
}

window.filterBillHistory = filterBillHistory;
window.renderBillHistory = renderBillHistory;

// Auto-render on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', renderBillHistory);
} else {
    setTimeout(renderBillHistory, 200);
}
</script>
