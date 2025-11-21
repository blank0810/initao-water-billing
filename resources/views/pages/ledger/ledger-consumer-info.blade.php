<div class="mb-6">
    <x-ui.button variant="outline" icon="fas fa-arrow-left" onclick="showLedgerTable()">
        Back to List
    </x-ui.button>
</div>

<div class="space-y-6">
    <!-- Consumer Profile -->
    <x-ui.card title="Consumer Profile">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-center">
                <i class="fas fa-user text-blue-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Name</span>
                    <div id="ledger_consumer_name" class="font-semibold text-gray-900 dark:text-white">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-id-card text-purple-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Consumer ID</span>
                    <div id="ledger_consumer_id" class="font-semibold text-gray-900 dark:text-white font-mono">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-tachometer-alt text-green-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Meter No.</span>
                    <div id="ledger_consumer_meter_no" class="font-semibold text-gray-900 dark:text-white font-mono">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-tag text-orange-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Account Type</span>
                    <div id="ledger_account_type" class="font-semibold text-gray-900 dark:text-white">-</div>
                </div>
            </div>
            <div class="flex items-center md:col-span-2">
                <i class="fas fa-map-marker-alt text-red-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Address</span>
                    <div id="ledger_consumer_address" class="font-semibold text-gray-900 dark:text-white">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-check-circle text-teal-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Status</span>
                    <div id="ledger_account_status" class="font-semibold text-gray-900 dark:text-white">-</div>
                </div>
            </div>
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 w-5 mr-3"></i>
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">Outstanding Balance</span>
                    <div id="ledger_outstanding_balance" class="font-bold text-red-600 dark:text-red-400 text-xl">-</div>
                </div>
            </div>
        </div>
    </x-ui.card>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900 dark:to-red-800 rounded-lg shadow p-4 border border-red-200 dark:border-red-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-red-600 dark:text-red-300 font-medium uppercase">Total Charges</div>
                    <div id="cardTotalCharges" class="text-2xl font-bold text-red-700 dark:text-red-200">₱ 0.00</div>
                </div>
                <i class="fas fa-file-invoice text-3xl text-red-400 dark:text-red-500"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg shadow p-4 border border-green-200 dark:border-green-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-green-600 dark:text-green-300 font-medium uppercase">Total Payments</div>
                    <div id="cardTotalPayments" class="text-2xl font-bold text-green-700 dark:text-green-200">₱ 0.00</div>
                </div>
                <i class="fas fa-money-bill-wave text-3xl text-green-400 dark:text-green-500"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg shadow p-4 border border-blue-200 dark:border-blue-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-blue-600 dark:text-blue-300 font-medium uppercase">Outstanding</div>
                    <div id="cardOutstandingBalance" class="text-2xl font-bold text-blue-700 dark:text-blue-200">₱ 0.00</div>
                </div>
                <i class="fas fa-balance-scale text-3xl text-blue-400 dark:text-blue-500"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900 dark:to-orange-800 rounded-lg shadow p-4 border border-orange-200 dark:border-orange-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-orange-600 dark:text-orange-300 font-medium uppercase">Penalties</div>
                    <div id="cardTotalPenalties" class="text-2xl font-bold text-orange-700 dark:text-orange-200">₱ 0.00</div>
                </div>
                <i class="fas fa-exclamation-triangle text-3xl text-orange-400 dark:text-orange-500"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-600 dark:text-gray-300 font-medium uppercase">Total Entries</div>
                    <div id="cardTotalEntries" class="text-2xl font-bold text-gray-700 dark:text-gray-200">0</div>
                </div>
                <i class="fas fa-list text-3xl text-gray-400 dark:text-gray-500"></i>
            </div>
        </div>
    </div>

    <!-- Source Type Breakdown -->
    <x-ui.card title="Transaction Type Breakdown">
        <div class="overflow-x-auto border rounded-lg shadow-sm bg-white dark:bg-gray-800">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Count</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Debit</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Credit</th>
                    </tr>
                </thead>
                <tbody id="sourceTypeBreakdownTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <!-- Ledger History -->
    <x-ui.card title="Ledger History">
        <div class="overflow-x-auto border rounded-lg shadow-sm bg-white dark:bg-gray-800">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reference</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Debit</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Credit</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Balance</th>
                    </tr>
                </thead>
                <tbody id="consumerLedgerTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <!-- Balance Trend Chart -->
    <x-ui.card title="Balance Trend Analysis">
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6 text-xs">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-600 rounded-full mr-2"></div>
                        <span class="text-gray-600 dark:text-gray-400">Balance Trend</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        <span class="text-gray-600 dark:text-gray-400">Debit (Owed)</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-gray-600 dark:text-gray-400">Credit (Paid)</span>
                    </div>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-chart-line mr-1"></i>Transaction History
                </div>
            </div>
        </div>
        <div id="ledgerChart" class="w-full h-56 flex items-end justify-center bg-gradient-to-b from-gray-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-lg p-4">
        </div>
    </x-ui.card>
</div>
