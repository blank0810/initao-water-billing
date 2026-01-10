<!-- Ledger Filter Component -->
@props([
    'id' => 'ledger-filter',
    'customers' => [],
    'periods' => [],
    'transactionTypes' => [],
    'onFilter' => null
])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        <i class="fas fa-filter mr-2"></i>Filter Ledger
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Customer Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Account</label>
            <select id="{{ $id }}_customer" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <option value="">All Customers</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer['id'] }}">{{ $customer['label'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Period Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Billing Period</label>
            <select id="{{ $id }}_period" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <option value="">All Periods</option>
                @foreach ($periods as $period)
                    <option value="{{ $period['id'] }}">{{ $period['label'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Transaction Type Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Transaction Type</label>
            <select id="{{ $id }}_type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <option value="">All Types</option>
                @foreach ($transactionTypes as $type)
                    <option value="{{ $type['id'] }}">{{ $type['label'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-end gap-2">
            <button id="{{ $id }}_filter" onclick="filterLedger('{{ $id }}')" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
            <button id="{{ $id }}_clear" onclick="clearLedgerFilter('{{ $id }}')" class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg font-medium transition">
                <i class="fas fa-times mr-2"></i>Clear
            </button>
        </div>
    </div>

    <!-- Status Bar -->
    <div id="{{ $id }}_status" class="mt-4 text-sm text-gray-600 dark:text-gray-400 hidden">
        Showing results for: <span id="{{ $id }}_status_text" class="font-semibold text-gray-900 dark:text-white"></span>
    </div>
</div>

<script>
window.filterLedger = function(filterId) {
    const customer = document.getElementById(filterId + '_customer').value;
    const period = document.getElementById(filterId + '_period').value;
    const type = document.getElementById(filterId + '_type').value;
    
    console.log('Ledger filter applied:', { customer, period, type });
    
    // Show status
    let status = [];
    if (customer) status.push('Customer: ' + customer);
    if (period) status.push('Period: ' + period);
    if (type) status.push('Type: ' + type);
    
    if (status.length > 0) {
        document.getElementById(filterId + '_status').classList.remove('hidden');
        document.getElementById(filterId + '_status_text').textContent = status.join(' | ');
    } else {
        document.getElementById(filterId + '_status').classList.add('hidden');
    }
};

window.clearLedgerFilter = function(filterId) {
    document.getElementById(filterId + '_customer').value = '';
    document.getElementById(filterId + '_period').value = '';
    document.getElementById(filterId + '_type').value = '';
    document.getElementById(filterId + '_status').classList.add('hidden');
    console.log('Ledger filter cleared');
};
</script>
