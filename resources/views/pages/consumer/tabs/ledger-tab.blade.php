<!-- Ledger Tab -->
<div id="ledger-content" class="tab-content hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-book mr-2 text-purple-600"></i>Consumer Ledger
        </h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Bill Period</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Amount Due</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Amount Paid</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Balance</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">January 2024</td>
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">₱1,250.00</td>
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">₱1,250.00</td>
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">₱0.00</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300">Paid</span>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">December 2023</td>
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">₱1,200.00</td>
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">₱1,200.00</td>
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">₱0.00</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300">Paid</span>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">November 2023</td>
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">₱1,180.00</td>
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">₱1,180.00</td>
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">₱0.00</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300">Paid</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg border border-blue-200 dark:border-blue-700">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Balance</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="total-balance">₱0.00</p>
                </div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>Export Ledger
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Update ledger balance when page loads
    document.addEventListener('DOMContentLoaded', function() {
        if (window.currentConsumer) {
            const totalBalanceEl = document.getElementById('total-balance');
            if (totalBalanceEl) {
                totalBalanceEl.textContent = window.currentConsumer.ledger_balance;
            }
        }
    });
</script>
