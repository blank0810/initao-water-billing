<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <!-- Page Header -->
            <x-ui.page-header 
                title="Billing Overall Data" 
                subtitle="Comprehensive billing analytics and insights"
                back-url="{{ route('billing.management') }}"
                back-text="Back to Billing Management"
            />

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <x-ui.stat-card 
                    title="Total Consumers" 
                    value="0" 
                    icon="fas fa-users" 
                    color="blue"
                    id="totalConsumers"
                />
                <x-ui.stat-card 
                    title="Total Amount Due" 
                    value="₱ 0.00" 
                    icon="fas fa-exclamation-triangle" 
                    color="red"
                    id="totalAmountDue"
                />
                <x-ui.stat-card 
                    title="Total Paid" 
                    value="₱ 0.00" 
                    icon="fas fa-check-circle" 
                    color="green"
                    id="totalPaid"
                />
                <x-ui.stat-card 
                    title="Outstanding Balance" 
                    value="₱ 0.00" 
                    icon="fas fa-clock" 
                    color="yellow"
                    id="outstandingBalance"
                />
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Payment Status Chart -->
                <x-ui.card class="h-80">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Status Distribution</h3>
                        <x-ui.export-print type="chart" target-id="paymentStatusChart" filename="payment-status-chart" title="Payment Status Distribution" />
                    </div>
                    <div class="relative h-64">
                        <canvas id="paymentStatusChart"></canvas>
                    </div>
                </x-ui.card>

                <!-- Monthly Revenue Trend -->
                <x-ui.card class="h-80">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Revenue Trend</h3>
                        <x-ui.export-print type="chart" target-id="revenueChart" filename="revenue-trend-chart" title="Monthly Revenue Trend" />
                    </div>
                    <div class="relative h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </x-ui.card>
            </div>

            <!-- Data Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Consumers by Amount Due -->
                <x-ui.card>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Consumers by Amount Due</h3>
                        <x-ui.export-print type="table" target-id="topConsumersTableFull" filename="top-consumers" title="Top Consumers by Amount Due" />
                    </div>
                    <div class="overflow-x-auto">
                        <table id="topConsumersTableFull" class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Consumer</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount Due</th>
                                </tr>
                            </thead>
                            <tbody id="topConsumersTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>

                <!-- Recent Payments -->
                <x-ui.card>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Payments</h3>
                        <x-ui.export-print type="table" target-id="recentPaymentsTableFull" filename="recent-payments" title="Recent Payments" />
                    </div>
                    <div class="overflow-x-auto">
                        <table id="recentPaymentsTableFull" class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Consumer</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody id="recentPaymentsTable" class="divide-y divide-gray-200 dark:divide-gray-700">
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
@vite(['resources/js/data/billing/bill-data.js', 'resources/js/export-print.js'])

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts and data
    initializeBillingOverallData();
});

function initializeBillingOverallData() {
    // Sample data - replace with actual data from your backend
    const billingData = {
        totalConsumers: 1250,
        totalAmountDue: 2850000,
        totalPaid: 1950000,
        outstandingBalance: 900000
    };

    // Update summary cards - find the value elements within the stat cards
    const totalConsumersCard = document.querySelector('#totalConsumers .text-2xl');
    const totalAmountDueCard = document.querySelector('#totalAmountDue .text-2xl');
    const totalPaidCard = document.querySelector('#totalPaid .text-2xl');
    const outstandingBalanceCard = document.querySelector('#outstandingBalance .text-2xl');
    
    if (totalConsumersCard) totalConsumersCard.textContent = billingData.totalConsumers.toLocaleString();
    if (totalAmountDueCard) totalAmountDueCard.textContent = '₱ ' + billingData.totalAmountDue.toLocaleString();
    if (totalPaidCard) totalPaidCard.textContent = '₱ ' + billingData.totalPaid.toLocaleString();
    if (outstandingBalanceCard) outstandingBalanceCard.textContent = '₱ ' + billingData.outstandingBalance.toLocaleString();

    // Initialize Payment Status Chart
    const paymentCtx = document.getElementById('paymentStatusChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Overdue', 'Pending'],
            datasets: [{
                data: [65, 20, 15],
                backgroundColor: ['#10B981', '#EF4444', '#F59E0B']
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

    // Initialize Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [320000, 285000, 340000, 310000, 365000, 380000],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
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

    // Populate tables
    populateTopConsumersTable();
    populateRecentPaymentsTable();
    
    // Make tables sortable
    setTimeout(() => {
        TableSorter.makeTableSortable('topConsumersTableFull');
        TableSorter.makeTableSortable('recentPaymentsTableFull');
    }, 100);
}

function populateTopConsumersTable() {
    const topConsumers = [
        { name: 'Juan Dela Cruz', amount: 15000 },
        { name: 'Maria Santos', amount: 12500 },
        { name: 'Pedro Garcia', amount: 11200 },
        { name: 'Ana Rodriguez', amount: 9800 },
        { name: 'Carlos Lopez', amount: 8900 }
    ];

    const tbody = document.getElementById('topConsumersTable');
    tbody.innerHTML = topConsumers.map(consumer => `
        <tr class="text-sm">
            <td class="px-4 py-2 text-gray-900 dark:text-white">${consumer.name}</td>
            <td class="px-4 py-2 text-red-600 dark:text-red-400">₱ ${consumer.amount.toLocaleString()}</td>
        </tr>
    `).join('');
}

function populateRecentPaymentsTable() {
    const recentPayments = [
        { name: 'Lisa Chen', amount: 2500, date: '2024-01-15' },
        { name: 'Mark Johnson', amount: 3200, date: '2024-01-14' },
        { name: 'Sofia Martinez', amount: 1800, date: '2024-01-14' },
        { name: 'David Kim', amount: 2900, date: '2024-01-13' },
        { name: 'Emma Wilson', amount: 2100, date: '2024-01-13' }
    ];

    const tbody = document.getElementById('recentPaymentsTable');
    tbody.innerHTML = recentPayments.map(payment => `
        <tr class="text-sm">
            <td class="px-4 py-2 text-gray-900 dark:text-white">${payment.name}</td>
            <td class="px-4 py-2 text-green-600 dark:text-green-400">₱ ${payment.amount.toLocaleString()}</td>
            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">${payment.date}</td>
        </tr>
    `).join('');
}
</script>