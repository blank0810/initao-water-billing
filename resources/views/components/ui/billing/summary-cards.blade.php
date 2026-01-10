<div id="billingSummaryCards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 w-full">
    <!-- Outstanding Balance -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">Outstanding Balance</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">₱6,527.50</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">8 accounts overdue</p>
            </div>
            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-circle text-gray-600 dark:text-gray-400 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Paid -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">Total Paid</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">₱178,923.00</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">287 payments processed</p>
            </div>
            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-gray-600 dark:text-gray-400 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Overdue Bills -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">Overdue Bills</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">8</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">2.68% of active accounts</p>
            </div>
            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-gray-600 dark:text-gray-400 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Adjustments -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">Total Adjustments</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">₱225.00</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">5 adjustments this month</p>
            </div>
            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                <i class="fas fa-edit text-gray-600 dark:text-gray-400 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Summary Cards Data (Calculated from Billing Data)
    const summaryStats = {
        outstanding: '₱6,527.50',
        overdue_accounts: 8,
        total_paid: '₱178,923.00',
        paid_transactions: 287,
        overdue_bills: 8,
        delinquency_rate: '2.68%',
        total_adjustments: '₱225.00',
        adjustments_count: 5
    };
    
    // Update card values
    if (window.billingData) {
        window.billingData.summaryStats = summaryStats;
    }
});
</script>
