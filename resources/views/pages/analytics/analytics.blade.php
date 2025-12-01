<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            
            <!-- Title -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Analytics Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Comprehensive overview of all system metrics and performance</p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Consumers</p>
                            <p id="totalConsumers" class="text-3xl font-bold mt-2">1,250</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Total Users</p>
                            <p id="totalUsers" class="text-3xl font-bold mt-2">45</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-user-cog text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Total Active</p>
                            <p id="totalActive" class="text-3xl font-bold mt-2">1,180</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-tachometer-alt text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Overall Bill</p>
                            <p id="overallBill" class="text-3xl font-bold mt-2">₱ 2.85M</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-file-invoice-dollar text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="switchTab('billing')" id="tab-billing" class="tab-button active py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600 dark:text-blue-400">
                            <i class="fas fa-file-invoice mr-2"></i>Billing
                        </button>
                        <button onclick="switchTab('meter')" id="tab-meter" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                            <i class="fas fa-tachometer-alt mr-2"></i>Meter
                        </button>
                        <button onclick="switchTab('ledger')" id="tab-ledger" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                            <i class="fas fa-book mr-2"></i>Ledger
                        </button>
                        <button onclick="switchTab('rate')" id="tab-rate" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                            <i class="fas fa-tags mr-2"></i>Rate
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Billing Tab -->
                    <div id="content-billing" class="tab-content">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Consumers</div>
                                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">1,250</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Amount Due</div>
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">₱ 2,850,000</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Paid</div>
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">₱ 1,950,000</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Outstanding Balance</div>
                                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">₱ 900,000</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Status Distribution</h4>
                                <div class="relative h-64">
                                    <canvas id="billingChart1"></canvas>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Revenue Trend</h4>
                                <div class="relative h-64">
                                    <canvas id="billingChart2"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meter Tab -->
                    <div id="content-meter" class="tab-content hidden">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Active Meters</div>
                                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">1,180</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Consumption</div>
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">45,680 m³</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Avg Consumption</div>
                                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">38.7 m³</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Overdue Readings</div>
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">23</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Consumption Trend</h4>
                                <div class="relative h-64">
                                    <canvas id="meterChart1"></canvas>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Meter Status Distribution</h4>
                                <div class="relative h-64">
                                    <canvas id="meterChart2"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ledger Tab -->
                    <div id="content-ledger" class="tab-content hidden">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Income</div>
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">₱ 3,250,000</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Expenses</div>
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">₱ 1,850,000</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Net Balance</div>
                                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">₱ 1,400,000</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Transactions</div>
                                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">2,847</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Income vs Expenses Trend</h4>
                                <div class="relative h-64">
                                    <canvas id="ledgerChart1"></canvas>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Transaction Categories</h4>
                                <div class="relative h-64">
                                    <canvas id="ledgerChart2"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rate Tab -->
                    <div id="content-rate" class="tab-content hidden">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Active Rate Plans</div>
                                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">5</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Avg Rate per m³</div>
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">₱ 28.50</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Total Customers</div>
                                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">1,250</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Revenue Impact</div>
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">₱ 1,850,000</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Rate Structure by Tier</h4>
                                <div class="relative h-64">
                                    <canvas id="rateChart1"></canvas>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Customer Distribution by Rate Plan</h4>
                                <div class="relative h-64">
                                    <canvas id="rateChart2"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let charts = {};

document.addEventListener('DOMContentLoaded', function() {
    initializeAnalytics();
});

function switchTab(tabName) {
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    });
    
    document.getElementById(`tab-${tabName}`).classList.add('active', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
    document.getElementById(`tab-${tabName}`).classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');

    // Update content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    document.getElementById(`content-${tabName}`).classList.remove('hidden');

    // Initialize charts for the active tab
    initializeTabCharts(tabName);
}

function initializeAnalytics() {
    initializeTabCharts('billing');
}

function initializeTabCharts(tabName) {
    // Destroy existing charts
    Object.values(charts).forEach(chart => {
        if (chart) chart.destroy();
    });
    charts = {};

    switch(tabName) {
        case 'billing':
            initializeBillingCharts();
            break;
        case 'meter':
            initializeMeterCharts();
            break;
        case 'ledger':
            initializeLedgerCharts();
            break;
        case 'rate':
            initializeRateCharts();
            break;
    }
}

function initializeBillingCharts() {
    const ctx1 = document.getElementById('billingChart1').getContext('2d');
    charts.billing1 = new Chart(ctx1, {
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
            plugins: { legend: { position: 'bottom' } }
        }
    });

    const ctx2 = document.getElementById('billingChart2').getContext('2d');
    charts.billing2 = new Chart(ctx2, {
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
            plugins: { legend: { position: 'top' } }
        }
    });
}

function initializeMeterCharts() {
    const ctx1 = document.getElementById('meterChart1').getContext('2d');
    charts.meter1 = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Consumption (m³)',
                data: [42000, 38500, 45200, 41800, 47300, 45680],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } }
        }
    });

    const ctx2 = document.getElementById('meterChart2').getContext('2d');
    charts.meter2 = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Maintenance', 'Inactive'],
            datasets: [{
                data: [85, 10, 5],
                backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
}

function initializeLedgerCharts() {
    const ctx1 = document.getElementById('ledgerChart1').getContext('2d');
    charts.ledger1 = new Chart(ctx1, {
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
            plugins: { legend: { position: 'top' } }
        }
    });

    const ctx2 = document.getElementById('ledgerChart2').getContext('2d');
    charts.ledger2 = new Chart(ctx2, {
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
            plugins: { legend: { position: 'bottom' } }
        }
    });
}

function initializeRateCharts() {
    const ctx1 = document.getElementById('rateChart1').getContext('2d');
    charts.rate1 = new Chart(ctx1, {
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
            plugins: { legend: { position: 'top' } }
        }
    });

    const ctx2 = document.getElementById('rateChart2').getContext('2d');
    charts.rate2 = new Chart(ctx2, {
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
            plugins: { legend: { position: 'bottom' } }
        }
    });
}
</script>

<style>
.tab-button.active {
    border-color: #3B82F6 !important;
    color: #3B82F6 !important;
}
</style>