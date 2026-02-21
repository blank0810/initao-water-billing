<!-- Connection Ledger Tab Content -->
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-book mr-2 text-blue-600 dark:text-blue-400"></i>
            Transaction Ledger
        </h3>
    </div>

    <!-- Filter Bar -->
    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-4">
        <div class="flex flex-wrap gap-4 items-end">
            <!-- Period Filter -->
            <div class="flex-1 min-w-[200px]">
                <label for="conn-ledger-period-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Billing Period
                </label>
                <select id="conn-ledger-period-filter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500"
                    onchange="filterConnectionLedger()">
                    <option value="">All Periods</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div class="flex-1 min-w-[200px]">
                <label for="conn-ledger-type-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Transaction Type
                </label>
                <select id="conn-ledger-type-filter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500"
                    onchange="filterConnectionLedger()">
                    <option value="">All Types</option>
                </select>
            </div>

            <!-- Reset Button -->
            <div>
                <button type="button" onclick="resetConnectionLedgerFilters()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-lg transition-colors">
                    <i class="fas fa-undo mr-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Time</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Description</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Debit</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Credit</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Balance</th>
                </tr>
            </thead>
            <tbody id="conn-ledger-tbody" class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Loading ledger data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div id="conn-ledger-pagination" class="px-4 py-3 bg-gray-50 dark:bg-gray-800 rounded-b-lg">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700 dark:text-gray-300">
                Showing <span id="conn-ledger-showing-start">0</span> to <span id="conn-ledger-showing-end">0</span> of <span id="conn-ledger-total">0</span> entries
            </div>
            <div class="flex gap-2" id="conn-ledger-pagination-buttons">
            </div>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="mt-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg border border-blue-200 dark:border-gray-600 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Debits</p>
                <p class="text-xl font-bold text-red-600 dark:text-red-400" id="conn-ledger-total-debit">&#8369;0.00</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Credits</p>
                <p class="text-xl font-bold text-green-600 dark:text-green-400" id="conn-ledger-total-credit">&#8369;0.00</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Net Balance</p>
                <p class="text-xl font-bold" id="conn-ledger-net-balance">&#8369;0.00</p>
            </div>
        </div>
    </div>
</div>
