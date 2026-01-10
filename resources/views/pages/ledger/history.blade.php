<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <x-ui.page-header 
                title="Complete Ledger History" 
                subtitle="Full chronological transaction history. All entries are immutable and audit-compliant."
            >
                <x-slot name="actions">
                    <x-ui.button variant="secondary" size="sm" onclick="downloadHistory()" icon="fas fa-download">
                        Download History
                    </x-ui.button>
                    <x-ui.button variant="primary" size="sm" onclick="window.print()" icon="fas fa-print">
                        Print Report
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-filter mr-2"></i>Filter Ledger History
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Account</label>
                        <select id="filterCustomer" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">All Customers</option>
                            <option value="ACC-2024-001">ACC-2024-001 - Juan Dela Cruz</option>
                            <option value="ACC-2024-002">ACC-2024-002 - Maria Santos</option>
                            <option value="ACC-2024-003">ACC-2024-003 - Pedro Garcia</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Billing Period</label>
                        <select id="filterPeriod" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">All Periods</option>
                            <option value="2024-01">January 2024</option>
                            <option value="2024-02">February 2024</option>
                            <option value="2023-12">December 2023</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Transaction Type</label>
                        <select id="filterType" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">All Types</option>
                            <option value="charge">Bill Charge</option>
                            <option value="payment">Payment</option>
                            <option value="adjustment">Adjustment</option>
                            <option value="penalty">Penalty</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end gap-2">
                        <button onclick="applyFilters()" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                        <button onclick="clearFilters()" class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg font-medium transition">
                            <i class="fas fa-times mr-2"></i>Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Total Entries</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">1,245</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Total Credits</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">₱125,450.00</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-orange-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Total Debits</p>
                    <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">₱128,920.00</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Net Balance</p>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">-₱3,470.00</p>
                </div>
            </div>

            <!-- Ledger History Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-list mr-2"></i>Transaction History
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                                <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Entry ID</th>
                                <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Customer</th>
                                <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Type</th>
                                <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Period</th>
                                <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Debit</th>
                                <th class="text-right py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Credit</th>
                                <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Date Posted</th>
                                <th class="text-left py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Status</th>
                                <th class="text-center py-3 px-4 text-gray-700 dark:text-gray-300 font-semibold text-sm">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-4 text-gray-900 dark:text-white font-mono">LDG-2024-0001</td>
                                <td class="py-3 px-4"><div class="text-sm font-medium text-gray-900 dark:text-white">Juan Dela Cruz</div><div class="text-xs text-gray-500">ACC-2024-001</div></td>
                                <td class="py-3 px-4"><span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded text-xs font-semibold">Bill Charge</span></td>
                                <td class="py-3 px-4 text-gray-900 dark:text-white">Jan 2024</td>
                                <td class="text-right py-3 px-4 text-orange-600 dark:text-orange-400 font-semibold">₱739.20</td>
                                <td class="text-right py-3 px-4 text-gray-600 dark:text-gray-400">-</td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400 text-sm">Jan 15, 2024</td>
                                <td class="py-3 px-4"><span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded text-xs font-semibold">Posted</span></td>
                                <td class="text-center py-3 px-4">
                                    <a href="/ledger/show/1" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium text-sm">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-4 text-gray-900 dark:text-white font-mono">LDG-2024-0002</td>
                                <td class="py-3 px-4"><div class="text-sm font-medium text-gray-900 dark:text-white">Juan Dela Cruz</div><div class="text-xs text-gray-500">ACC-2024-001</div></td>
                                <td class="py-3 px-4"><span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded text-xs font-semibold">Payment</span></td>
                                <td class="py-3 px-4 text-gray-900 dark:text-white">Jan 2024</td>
                                <td class="text-right py-3 px-4 text-gray-600 dark:text-gray-400">-</td>
                                <td class="text-right py-3 px-4 text-green-600 dark:text-green-400 font-semibold">₱500.00</td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400 text-sm">Jan 20, 2024</td>
                                <td class="py-3 px-4"><span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded text-xs font-semibold">Posted</span></td>
                                <td class="text-center py-3 px-4">
                                    <a href="/ledger/show/2" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium text-sm">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-3 px-4 text-gray-900 dark:text-white font-mono">LDG-2024-0003</td>
                                <td class="py-3 px-4"><div class="text-sm font-medium text-gray-900 dark:text-white">Juan Dela Cruz</div><div class="text-xs text-gray-500">ACC-2024-001</div></td>
                                <td class="py-3 px-4"><span class="px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 rounded text-xs font-semibold">Adjustment</span></td>
                                <td class="py-3 px-4 text-gray-900 dark:text-white">Jan 2024</td>
                                <td class="text-right py-3 px-4 text-gray-600 dark:text-gray-400">-</td>
                                <td class="text-right py-3 px-4 text-green-600 dark:text-green-400 font-semibold">₱55.80</td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400 text-sm">Jan 18, 2024</td>
                                <td class="py-3 px-4"><span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded text-xs font-semibold">Posted</span></td>
                                <td class="text-center py-3 px-4">
                                    <a href="/ledger/show/3" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium text-sm">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
                    <span>Showing 3 of 1,245 entries</span>
                    <div class="flex gap-2">
                        <button class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-200 dark:hover:bg-gray-600">Previous</button>
                        <button class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-200 dark:hover:bg-gray-600">Next</button>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Ledger History is Immutable:</strong> All entries in this ledger are permanent and cannot be modified or deleted. This ensures complete audit trail compliance and data integrity for financial reporting.
                </p>
            </div>
        </div>
    </div>

    @vite([
        'resources/js/utils/action-functions.js'
    ])

    <script>
    function applyFilters() {
        const customer = document.getElementById('filterCustomer').value;
        const period = document.getElementById('filterPeriod').value;
        const type = document.getElementById('filterType').value;
        console.log('Applying filters:', { customer, period, type });
        // Filter logic here
    }

    function clearFilters() {
        document.getElementById('filterCustomer').value = '';
        document.getElementById('filterPeriod').value = '';
        document.getElementById('filterType').value = '';
        console.log('Filters cleared');
    }

    function downloadHistory() {
        alert('Download ledger history as report');
    }

    window.applyFilters = applyFilters;
    window.clearFilters = clearFilters;
    window.downloadHistory = downloadHistory;
    </script>
</x-app-layout>
