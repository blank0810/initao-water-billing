<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <!-- Page Header -->
            <x-ui.page-header 
                title="Ledger Overall Data" 
                subtitle="Comprehensive financial ledger and transaction analytics"
                back-url="{{ route('ledger.management') }}"
                back-text="Back to Ledger Management"
            />

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <x-ui.stat-card 
                    title="Total Income" 
                    value="₱ 0.00" 
                    icon="fas fa-arrow-up" 
                    id="totalIncome"
                />
                <x-ui.stat-card 
                    title="Total Expenses" 
                    value="₱ 0.00" 
                    icon="fas fa-arrow-down" 
                    id="totalExpenses"
                />
                <x-ui.stat-card 
                    title="Net Balance" 
                    value="₱ 0.00" 
                    icon="fas fa-balance-scale" 
                    id="netBalance"
                />
                <x-ui.stat-card 
                    title="Total Transactions" 
                    value="0" 
                    icon="fas fa-receipt" 
                    id="totalTransactions"
                />
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Income vs Expenses Chart -->
                <x-ui.card class="h-80">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Income vs Expenses Trend</h3>
                        <x-ui.export-print type="chart" target-id="incomeExpenseChart" filename="income-expenses" title="Income vs Expenses Trend" />
                    </div>
                    <div class="relative h-64">
                        <canvas id="incomeExpenseChart"></canvas>
                    </div>
                </x-ui.card>

                <!-- Transaction Categories -->
                <x-ui.card class="h-80">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaction Categories</h3>
                        <x-ui.export-print type="chart" target-id="categoriesChart" filename="transaction-categories" title="Transaction Categories" />
                    </div>
                    <div class="relative h-64">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </x-ui.card>
            </div>

            <!-- Data Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Transactions -->
                <x-ui.card>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Transactions</h3>
                        <x-ui.export-print type="table" target-id="recentTransactionsTableFull" filename="recent-transactions" title="Recent Transactions" />
                    </div>
                    <div class="overflow-x-auto">
                        <table id="recentTransactionsTableFull" class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody id="recentTransactionsTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>

                <!-- Monthly Summary -->
                <x-ui.card>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Summary</h3>
                        <x-ui.export-print type="table" target-id="monthlySummaryTableFull" filename="monthly-summary" title="Monthly Summary" />
                    </div>
                    <div class="overflow-x-auto">
                        <table id="monthlySummaryTableFull" class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Month</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Income</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Expenses</th>
                                </tr>
                            </thead>
                            <tbody id="monthlySummaryTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>
            </div>

        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@vite(['resources/js/export-print.js'])

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeLedgerOverallData();
});

function initializeLedgerOverallData() {
    const ledgerData = {
        totalIncome: 3250000,
        totalExpenses: 1850000,
        netBalance: 1400000,
        totalTransactions: 2847
    };

    // Update summary cards - find the value elements within the stat cards
    const totalIncomeCard = document.querySelector('#totalIncome .text-2xl');
    const totalExpensesCard = document.querySelector('#totalExpenses .text-2xl');
    const netBalanceCard = document.querySelector('#netBalance .text-2xl');
    const totalTransactionsCard = document.querySelector('#totalTransactions .text-2xl');
    
    if (totalIncomeCard) totalIncomeCard.textContent = '₱ ' + ledgerData.totalIncome.toLocaleString();
    if (totalExpensesCard) totalExpensesCard.textContent = '₱ ' + ledgerData.totalExpenses.toLocaleString();
    if (netBalanceCard) netBalanceCard.textContent = '₱ ' + ledgerData.netBalance.toLocaleString();
    if (totalTransactionsCard) totalTransactionsCard.textContent = ledgerData.totalTransactions.toLocaleString();

    // Initialize Income vs Expenses Chart
    const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
    new Chart(incomeExpenseCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Income',
                data: [520000, 485000, 560000, 530000, 595000, 560000],
                backgroundColor: '#10B981'
            }, {
                label: 'Expenses',
                data: [320000, 285000, 340000, 310000, 365000, 330000],
                backgroundColor: '#EF4444'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Initialize Categories Chart
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    new Chart(categoriesCtx, {
        type: 'doughnut',
        data: {
            labels: ['Water Bills', 'Maintenance', 'Operations', 'Utilities', 'Other'],
            datasets: [{
                data: [45, 20, 15, 12, 8],
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    populateRecentTransactionsTable();
    populateMonthlySummaryTable();
    
    // Make tables sortable
    setTimeout(() => {
        TableSorter.makeTableSortable('recentTransactionsTableFull');
        TableSorter.makeTableSortable('monthlySummaryTableFull');
    }, 100);
}

function populateRecentTransactionsTable() {
    const recentTransactions = [
        { description: 'Water Bill Payment - Juan Cruz', amount: 2500, date: '2024-01-15', type: 'income' },
        { description: 'Maintenance - Pump Repair', amount: -8500, date: '2024-01-14', type: 'expense' },
        { description: 'Water Bill Payment - Maria Santos', amount: 3200, date: '2024-01-14', type: 'income' },
        { description: 'Utility Bill - Electricity', amount: -12000, date: '2024-01-13', type: 'expense' },
        { description: 'Water Bill Payment - Pedro Garcia', amount: 1800, date: '2024-01-13', type: 'income' }
    ];

    const tbody = document.getElementById('recentTransactionsTable');
    tbody.innerHTML = recentTransactions.map(transaction => `
        <tr class="text-sm">
            <td class="px-4 py-2 text-gray-900 dark:text-white">${transaction.description}</td>
            <td class="px-4 py-2 ${transaction.type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}">
                ₱ ${Math.abs(transaction.amount).toLocaleString()}
            </td>
            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">${transaction.date}</td>
        </tr>
    `).join('');
}

function populateMonthlySummaryTable() {
    const monthlySummary = [
        { month: 'January', income: 560000, expenses: 330000 },
        { month: 'December', income: 595000, expenses: 365000 },
        { month: 'November', income: 530000, expenses: 310000 },
        { month: 'October', income: 560000, expenses: 340000 },
        { month: 'September', income: 485000, expenses: 285000 }
    ];

    const tbody = document.getElementById('monthlySummaryTable');
    tbody.innerHTML = monthlySummary.map(summary => `
        <tr class="text-sm">
            <td class="px-4 py-2 text-gray-900 dark:text-white">${summary.month}</td>
            <td class="px-4 py-2 text-green-600 dark:text-green-400">₱ ${summary.income.toLocaleString()}</td>
            <td class="px-4 py-2 text-red-600 dark:text-red-400">₱ ${summary.expenses.toLocaleString()}</td>
        </tr>
    `).join('');
}
</script>