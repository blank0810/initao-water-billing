<!-- Ledger Tab Content -->
<div id="ledger-content" class="tab-content hidden">
    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4">
        <div class="flex flex-wrap gap-4 items-end">
            <!-- Connection Filter -->
            <div class="flex-1 min-w-[200px]">
                <label for="ledger-connection-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Service Connection
                </label>
                <select id="ledger-connection-filter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500"
                    onchange="filterLedger()">
                    <option value="">All Connections</option>
                </select>
            </div>

            <!-- Period Filter -->
            <div class="flex-1 min-w-[200px]">
                <label for="ledger-period-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Billing Period
                </label>
                <select id="ledger-period-filter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500"
                    onchange="filterLedger()">
                    <option value="">All Periods</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div class="flex-1 min-w-[200px]">
                <label for="ledger-type-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Transaction Type
                </label>
                <select id="ledger-type-filter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500"
                    onchange="filterLedger()">
                    <option value="">All Types</option>
                </select>
            </div>

            <!-- Reset Button -->
            <div>
                <button type="button" onclick="resetLedgerFilters()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    <i class="fas fa-undo mr-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Time</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Connection</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Debit</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Credit</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Balance</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="ledger-tbody" class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Loading ledger data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="ledger-pagination" class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Showing <span id="ledger-showing-start">0</span> to <span id="ledger-showing-end">0</span> of <span id="ledger-total">0</span> entries
                </div>
                <div class="flex gap-2" id="ledger-pagination-buttons">
                    <!-- Pagination buttons will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="mt-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg border border-blue-200 dark:border-gray-600 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Debits</p>
                <p class="text-xl font-bold text-red-600 dark:text-red-400" id="ledger-total-debit">&#8369;0.00</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Credits</p>
                <p class="text-xl font-bold text-green-600 dark:text-green-400" id="ledger-total-credit">&#8369;0.00</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Net Balance</p>
                <p class="text-xl font-bold" id="ledger-net-balance">&#8369;0.00</p>
            </div>
            <div class="text-center">
                <button onclick="exportLedger()"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>Export Ledger
                </button>
            </div>
        </div>
    </div>
</div>
