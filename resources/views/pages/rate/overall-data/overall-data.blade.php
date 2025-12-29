<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <!-- Page Header -->
            <x-ui.page-header 
                title="Rate Overall Data" 
                subtitle="Comprehensive water rate structures and pricing analytics"
                back-url="{{ route('rate.management') }}"
                back-text="Back to Rate Management"
            />

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <x-ui.stat-card 
                    title="Active Rate Plans" 
                    value="0" 
                    icon="fas fa-tags" 
                    id="activeRatePlans"
                />
                <x-ui.stat-card 
                    title="Avg Rate per m³" 
                    value="₱ 0.00" 
                    icon="fas fa-dollar-sign" 
                    id="avgRate"
                />
                <x-ui.stat-card 
                    title="Total Customers" 
                    value="0" 
                    icon="fas fa-users" 
                    id="totalCustomers"
                />
                <x-ui.stat-card 
                    title="Revenue Impact" 
                    value="₱ 0.00" 
                    icon="fas fa-chart-line" 
                    id="revenueImpact"
                />
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Rate Structure Chart -->
                <x-ui.card class="h-80">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Rate Structure by Tier</h3>
                        <x-ui.export-print type="chart" target-id="rateStructureChart" filename="rate-structure" title="Rate Structure by Tier" />
                    </div>
                    <div class="relative h-64">
                        <canvas id="rateStructureChart"></canvas>
                    </div>
                </x-ui.card>

                <!-- Customer Distribution by Rate Plan -->
                <x-ui.card class="h-80">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Customer Distribution by Rate Plan</h3>
                        <x-ui.export-print type="chart" target-id="customerDistributionChart" filename="customer-distribution" title="Customer Distribution by Rate Plan" />
                    </div>
                    <div class="relative h-64">
                        <canvas id="customerDistributionChart"></canvas>
                    </div>
                </x-ui.card>
            </div>

            <!-- Data Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Current Rate Plans -->
                <x-ui.card>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Current Rate Plans</h3>
                        <x-ui.export-print type="table" target-id="ratePlansTableFull" filename="rate-plans" title="Current Rate Plans" />
                    </div>
                    <div class="overflow-x-auto">
                        <table id="ratePlansTableFull" class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Plan Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rate (₱/m³)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customers</th>
                                </tr>
                            </thead>
                            <tbody id="ratePlansTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>

                <!-- Rate History -->
                <x-ui.card>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Rate Changes</h3>
                        <x-ui.export-print type="table" target-id="rateHistoryTableFull" filename="rate-history" title="Recent Rate Changes" />
                    </div>
                    <div class="overflow-x-auto">
                        <table id="rateHistoryTableFull" class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Plan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Old Rate</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">New Rate</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody id="rateHistoryTable" class="divide-y divide-gray-200 dark:divide-gray-700">
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
    initializeRateOverallData();
});

function initializeRateOverallData() {
    const rateData = {
        activeRatePlans: 5,
        avgRate: 28.50,
        totalCustomers: 1250,
        revenueImpact: 1850000
    };

    // Update summary cards - find the value elements within the stat cards
    const activeRatePlansCard = document.querySelector('#activeRatePlans .text-2xl');
    const avgRateCard = document.querySelector('#avgRate .text-2xl');
    const totalCustomersCard = document.querySelector('#totalCustomers .text-2xl');
    const revenueImpactCard = document.querySelector('#revenueImpact .text-2xl');
    
    if (activeRatePlansCard) activeRatePlansCard.textContent = rateData.activeRatePlans;
    if (avgRateCard) avgRateCard.textContent = '₱ ' + rateData.avgRate.toFixed(2);
    if (totalCustomersCard) totalCustomersCard.textContent = rateData.totalCustomers.toLocaleString();
    if (revenueImpactCard) revenueImpactCard.textContent = '₱ ' + rateData.revenueImpact.toLocaleString();

    // Initialize Rate Structure Chart
    const rateStructureCtx = document.getElementById('rateStructureChart').getContext('2d');
    new Chart(rateStructureCtx, {
        type: 'bar',
        data: {
            labels: ['0-10 m³', '11-20 m³', '21-30 m³', '31-50 m³', '50+ m³'],
            datasets: [{
                label: 'Rate (₱/m³)',
                data: [15.00, 22.50, 28.00, 35.00, 42.50],
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
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
                            return '₱' + value.toFixed(2);
                        }
                    }
                }
            }
        }
    });

    // Initialize Customer Distribution Chart
    const customerDistributionCtx = document.getElementById('customerDistributionChart').getContext('2d');
    new Chart(customerDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Residential', 'Commercial', 'Industrial', 'Institutional', 'Bulk'],
            datasets: [{
                data: [65, 20, 8, 5, 2],
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

    populateRatePlansTable();
    populateRateHistoryTable();
    
    // Make tables sortable
    setTimeout(() => {
        TableSorter.makeTableSortable('ratePlansTableFull');
        TableSorter.makeTableSortable('rateHistoryTableFull');
    }, 100);
}

function populateRatePlansTable() {
    const ratePlans = [
        { name: 'Residential Basic', rate: 18.50, customers: 812 },
        { name: 'Commercial Standard', rate: 32.00, customers: 250 },
        { name: 'Industrial', rate: 45.00, customers: 98 },
        { name: 'Institutional', rate: 28.00, customers: 65 },
        { name: 'Bulk Supply', rate: 52.00, customers: 25 }
    ];

    const tbody = document.getElementById('ratePlansTable');
    tbody.innerHTML = ratePlans.map(plan => `
        <tr class="text-sm">
            <td class="px-4 py-2 text-gray-900 dark:text-white">${plan.name}</td>
            <td class="px-4 py-2 text-blue-600 dark:text-blue-400">₱ ${plan.rate.toFixed(2)}</td>
            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">${plan.customers}</td>
        </tr>
    `).join('');
}

function populateRateHistoryTable() {
    const rateHistory = [
        { plan: 'Residential Basic', oldRate: 16.50, newRate: 18.50, date: '2024-01-01' },
        { plan: 'Commercial Standard', oldRate: 28.00, newRate: 32.00, date: '2024-01-01' },
        { plan: 'Industrial', oldRate: 42.00, newRate: 45.00, date: '2023-12-01' },
        { plan: 'Institutional', oldRate: 25.00, newRate: 28.00, date: '2023-12-01' },
        { plan: 'Bulk Supply', oldRate: 48.00, newRate: 52.00, date: '2023-11-15' }
    ];

    const tbody = document.getElementById('rateHistoryTable');
    tbody.innerHTML = rateHistory.map(history => `
        <tr class="text-sm">
            <td class="px-4 py-2 text-gray-900 dark:text-white">${history.plan}</td>
            <td class="px-4 py-2 text-red-600 dark:text-red-400">₱ ${history.oldRate.toFixed(2)}</td>
            <td class="px-4 py-2 text-green-600 dark:text-green-400">₱ ${history.newRate.toFixed(2)}</td>
            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">${history.date}</td>
        </tr>
    `).join('');
}
</script>