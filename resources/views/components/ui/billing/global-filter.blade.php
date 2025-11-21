<div class="mb-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
            <input type="text" id="globalSearch" placeholder="Search..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
            <select id="globalStatusFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="2">Paid</option>
                <option value="3">Cancelled</option>
                <option value="4">Overdue</option>
                <option value="5">Adjusted</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
            <input type="date" id="globalDateFrom" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date To</label>
            <input type="date" id="globalDateTo" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
        </div>
    </div>
    <div class="flex justify-end gap-2 mt-4">
        <button onclick="applyGlobalFilter()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
            <i class="fas fa-filter mr-2"></i>Apply Filter
        </button>
        <button onclick="clearGlobalFilter()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">
            <i class="fas fa-times mr-2"></i>Clear
        </button>
    </div>
</div>

<script>
function applyGlobalFilter() {
    const search = document.getElementById('globalSearch').value.toLowerCase();
    const status = document.getElementById('globalStatusFilter').value;
    const dateFrom = document.getElementById('globalDateFrom').value;
    const dateTo = document.getElementById('globalDateTo').value;
    
    window.currentFilter = { search, status, dateFrom, dateTo };
    
    if (typeof renderCurrentTab === 'function') {
        renderCurrentTab();
    }
}

function clearGlobalFilter() {
    document.getElementById('globalSearch').value = '';
    document.getElementById('globalStatusFilter').value = '';
    document.getElementById('globalDateFrom').value = '';
    document.getElementById('globalDateTo').value = '';
    window.currentFilter = null;
    
    if (typeof renderCurrentTab === 'function') {
        renderCurrentTab();
    }
}

window.applyGlobalFilter = applyGlobalFilter;
window.clearGlobalFilter = clearGlobalFilter;
window.currentFilter = null;
</script>
